<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeLabCapture;
use App\Models\LabAgent;
use App\Models\LabCapture;
use Database\Factories\LabAgentFactory;
use Database\Factories\LabCaptureFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LabAgentApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function agentHeaders(string $token = LabAgentFactory::PLAIN_TOKEN): array
    {
        return ['Authorization' => "Bearer {$token}", 'Accept' => 'application/json'];
    }

    /**
     * @return array<string, mixed>
     */
    private function capturePayload(string $rawText = LabCaptureFactory::SAMPLE_RAW_TEXT): array
    {
        return [
            'raw_text' => $rawText,
            'raw_hash' => hash('sha256', $rawText),
            'captured_at' => '2026-10-28T14:03:11-03:00',
            'agent_version' => '0.1.0',
            'serial_settings' => ['port' => 'COM13', 'baudrate' => 57600, 'rtscts' => true],
        ];
    }

    public function test_ping_returns_agent_and_machine_and_records_heartbeat(): void
    {
        $agent = LabAgent::factory()->create();

        $this->withHeaders($this->agentHeaders())
            ->getJson(route('agent.ping', ['agent_version' => '0.1.0']))
            ->assertOk()
            ->assertJsonPath('data.agent.id', $agent->id)
            ->assertJsonPath('data.machine.serial_number', $agent->machine->serial_number);

        $agent->refresh();
        $this->assertNotNull($agent->last_seen_at);
        $this->assertSame('0.1.0', $agent->agent_version);
    }

    public function test_invalid_token_is_rejected(): void
    {
        LabAgent::factory()->create();

        $this->withHeaders($this->agentHeaders('wrong-token'))
            ->getJson(route('agent.ping'))
            ->assertUnauthorized();

        $this->getJson(route('agent.ping'))->assertUnauthorized();
    }

    public function test_inactive_agent_is_forbidden(): void
    {
        LabAgent::factory()->inactive()->create();

        $this->withHeaders($this->agentHeaders())
            ->getJson(route('agent.ping'))
            ->assertForbidden();
    }

    public function test_store_saves_capture_and_queues_analysis(): void
    {
        Queue::fake();
        $agent = LabAgent::factory()->create();

        $this->withHeaders($this->agentHeaders())
            ->postJson(route('agent.lab-captures.store'), $this->capturePayload())
            ->assertCreated()
            ->assertJsonPath('data.status', LabCapture::STATUS_RECEIVED)
            ->assertJsonPath('data.duplicate', false);

        $capture = LabCapture::firstOrFail();
        $this->assertSame($agent->id, $capture->lab_agent_id);
        $this->assertSame($agent->machine_id, $capture->machine_id);
        $this->assertStringContainsString('TTPA', $capture->raw_text);
        $this->assertSame('COM13', $capture->serial_settings['port']);
        $this->assertSame('2026-10-28 17:03:11', $capture->captured_at->utc()->format('Y-m-d H:i:s'));

        Queue::assertPushed(AnalyzeLabCapture::class, fn (AnalyzeLabCapture $job) => $job->labCapture->is($capture));
    }

    public function test_store_normalizes_line_breaks_and_deduplicates_by_content(): void
    {
        Queue::fake();
        LabAgent::factory()->create();
        $windowsStyle = str_replace("\n", "\r\n", LabCaptureFactory::SAMPLE_RAW_TEXT);

        $this->withHeaders($this->agentHeaders())
            ->postJson(route('agent.lab-captures.store'), $this->capturePayload($windowsStyle))
            ->assertCreated();

        $this->withHeaders($this->agentHeaders())
            ->postJson(route('agent.lab-captures.store'), $this->capturePayload(LabCaptureFactory::SAMPLE_RAW_TEXT))
            ->assertOk()
            ->assertJsonPath('data.duplicate', true);

        $this->assertDatabaseCount('lab_captures', 1);
        $this->assertStringNotContainsString("\r", LabCapture::firstOrFail()->raw_text);
        Queue::assertPushed(AnalyzeLabCapture::class, 1);
    }

    public function test_store_still_succeeds_when_sync_analysis_fails(): void
    {
        config()->set('services.gemini.api_key', 'test-key');
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['error' => ['message' => 'quota exceeded']], 429)]);
        LabAgent::factory()->create();

        $this->withHeaders($this->agentHeaders())
            ->postJson(route('agent.lab-captures.store'), $this->capturePayload())
            ->assertCreated();

        $capture = LabCapture::firstOrFail();
        $this->assertSame(LabCapture::STATUS_FAILED, $capture->status);
        $this->assertStringContainsString('quota exceeded', (string) $capture->error_message);
    }

    public function test_store_validates_payload(): void
    {
        LabAgent::factory()->create();

        $this->withHeaders($this->agentHeaders())
            ->postJson(route('agent.lab-captures.store'), ['raw_text' => '', 'captured_at' => 'ontem'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['raw_text', 'captured_at']);
    }
}
