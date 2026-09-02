<?php

namespace Tests\Feature;

use App\Jobs\GenerateHealthCampaign;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\HealthCampaign;
use App\Models\Patient;
use App\Models\User;
use App\Service\Ai\TextGenerationException;
use App\Service\ExamService;
use App\Service\HealthCampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class HealthCampaignTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_URL_PATTERN = 'generativelanguage.googleapis.com/*';

    private const PATIENT_NAME = 'Maria Segredo Silva';

    private const PATIENT_EMAIL = 'maria.segredo@example.com';

    private const PATIENT_CPF = '52998224725';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.gemini.api_key', 'test-key');
        config()->set('services.gemini.model', 'gemini-test');
    }

    /**
     * @return array<string, mixed>
     */
    private function analysisPayload(): array
    {
        return [
            'summary' => 'A maioria apresenta glicemia elevada.',
            'frequent_problems' => [
                ['problem' => 'Hiperglicemia', 'affected_patients' => 1, 'evidence' => 'Glicose acima de 99 mg/dL.'],
            ],
            'main_problem' => ['name' => 'Diabetes', 'description' => 'Glicemia de jejum elevada.', 'affected_share' => '1 de 1 (100%)'],
            'severity' => 'Alta',
            'urgency' => 'Alta',
            'risk_factors' => ['Sedentarismo'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function campaignPayload(): array
    {
        return [
            'name' => 'Comunidade Doce na Medida',
            'objective' => 'Reduzir a glicemia média.',
            'target_audience' => 'Adultos',
            'actions' => [['title' => 'Mutirão de glicemia', 'description' => 'Testes gratuitos na UBS.']],
            'materials' => ['Glicosímetros'],
            'partners' => ['UBS'],
            'timeline' => ['short_term' => ['Mutirão'], 'medium_term' => ['Oficinas'], 'long_term' => ['Acompanhamento']],
            'success_indicators' => ['Redução de 10% na glicemia média'],
            'key_messages' => ['Meça sua glicemia.'],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function geminiResponse(array $payload): array
    {
        return [
            'candidates' => [
                ['content' => ['parts' => [['text' => json_encode($payload, JSON_UNESCAPED_UNICODE)]], 'role' => 'model']],
            ],
        ];
    }

    private function fakeGemini(): void
    {
        Http::fake([
            self::GEMINI_URL_PATTERN => Http::sequence()
                ->push($this->geminiResponse($this->analysisPayload()))
                ->push($this->geminiResponse($this->campaignPayload())),
        ]);
    }

    private function createApprovedGlucoseExam(): Exam
    {
        $patientUser = User::factory()->patient()->create(['name' => self::PATIENT_NAME, 'email' => self::PATIENT_EMAIL]);
        $patient = Patient::factory()->create([
            'user_id' => $patientUser->id,
            'cpf' => self::PATIENT_CPF,
            'sex' => 'female',
            'birthday' => '1985-04-20',
        ]);
        $examType = ExamType::factory()->create(['name' => 'Glicemia']);
        $field = $examType->fields()->create(['name' => 'glucose', 'label' => 'Glicose', 'field_type' => 'float', 'unit' => 'mg/dL']);
        $field->references()->create(['min_value' => 70, 'max_value' => 99]);

        $exam = Exam::factory()->create([
            'patient_id' => $patient->id,
            'exam_type_id' => $examType->id,
            'date' => '2026-08-15',
            'results' => ['glucose' => 186],
        ]);
        $exam->status = ExamService::STATUS_APPROVED;
        $exam->save();

        return $exam;
    }

    public function test_store_saves_pending_campaign_with_anonymized_dataset_and_queues_the_job(): void
    {
        Queue::fake();
        $teacher = User::factory()->teacher()->create();
        $this->createApprovedGlucoseExam();

        $response = $this->actingAs($teacher)->post(route('health-campaigns.store'), [
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-31',
        ]);

        $campaign = HealthCampaign::firstOrFail();
        $response->assertRedirect(route('health-campaigns.show', $campaign->id));

        $this->assertSame($teacher->id, $campaign->user_id);
        $this->assertSame(HealthCampaign::STATUS_PENDING, $campaign->status);
        $this->assertSame(1, $campaign->patients_count);
        $this->assertSame(1, $campaign->exams_count);
        $this->assertNull($campaign->analysis);
        $this->assertNull($campaign->campaign);

        Queue::assertPushed(GenerateHealthCampaign::class, fn (GenerateHealthCampaign $job) => $job->healthCampaign->is($campaign));

        $this->assertStringContainsString('Paciente 1 (Feminino, 40-49 anos)', $campaign->dataset);
        $this->assertStringContainsString('Glicemia / Glicose: 186 mg/dL (Ref: 70 a 99) [Acima]', $campaign->dataset);
        $this->assertStringNotContainsString(self::PATIENT_NAME, $campaign->dataset);
        $this->assertStringNotContainsString(self::PATIENT_EMAIL, $campaign->dataset);
        $this->assertStringNotContainsString(self::PATIENT_CPF, $campaign->dataset);
        $this->assertStringNotContainsString('1985', $campaign->dataset);
    }

    public function test_job_fills_analysis_and_campaign_from_the_ai(): void
    {
        $this->fakeGemini();
        $teacher = User::factory()->teacher()->create();
        $this->createApprovedGlucoseExam();
        $campaign = $this->pendingCampaignFor($teacher);

        (new GenerateHealthCampaign($campaign))->handle(app(HealthCampaignService::class));

        $campaign->refresh();
        $this->assertSame(HealthCampaign::STATUS_COMPLETED, $campaign->status);
        $this->assertSame('gemini-test', $campaign->model);
        $this->assertSame('Comunidade Doce na Medida', $campaign->campaign['name']);
        $this->assertSame('Diabetes', $campaign->analysis['main_problem']['name']);

        Http::assertSentCount(2);
        Http::assertSent(function (Request $request): bool {
            $body = $request->data();

            return $request->hasHeader('x-goog-api-key', 'test-key')
                && str_contains($request->url(), 'models/gemini-test:generateContent')
                && ($body['generationConfig']['responseMimeType'] ?? null) === 'application/json'
                && ! str_contains(json_encode($body), self::PATIENT_NAME);
        });
    }

    public function test_sync_queue_completes_campaign_right_after_store(): void
    {
        $this->fakeGemini();
        $teacher = User::factory()->teacher()->create();
        $this->createApprovedGlucoseExam();

        $this->actingAs($teacher)->post(route('health-campaigns.store'), []);

        $this->assertSame(HealthCampaign::STATUS_COMPLETED, HealthCampaign::firstOrFail()->status);
    }

    public function test_failed_campaign_can_be_retried(): void
    {
        Queue::fake();
        $teacher = User::factory()->teacher()->create();
        $this->createApprovedGlucoseExam();
        $campaign = $this->pendingCampaignFor($teacher);
        $campaign->update(['status' => HealthCampaign::STATUS_FAILED, 'error_message' => 'quota exceeded']);

        $this->actingAs($teacher)
            ->from(route('health-campaigns.show', $campaign->id))
            ->post(route('health-campaigns.retry', $campaign->id))
            ->assertRedirect(route('health-campaigns.show', $campaign->id))
            ->assertSessionHas('success');

        $campaign->refresh();
        $this->assertSame(HealthCampaign::STATUS_PENDING, $campaign->status);
        $this->assertNull($campaign->error_message);
        Queue::assertPushed(GenerateHealthCampaign::class);
    }

    private function pendingCampaignFor(User $teacher): HealthCampaign
    {
        Queue::fake();
        $this->actingAs($teacher)->post(route('health-campaigns.store'), []);

        return HealthCampaign::firstOrFail();
    }

    public function test_generation_fails_when_there_are_no_approved_exams(): void
    {
        Queue::fake();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)
            ->from(route('health-campaigns.create'))
            ->post(route('health-campaigns.store'), [])
            ->assertRedirect(route('health-campaigns.create'))
            ->assertSessionHasErrors('date_from');

        Queue::assertNothingPushed();
        $this->assertDatabaseCount('health_campaigns', 0);
    }

    public function test_provider_failure_marks_campaign_as_failed(): void
    {
        Http::fake([self::GEMINI_URL_PATTERN => Http::response(['error' => ['message' => 'quota exceeded']], 429)]);
        $teacher = User::factory()->teacher()->create();
        $this->createApprovedGlucoseExam();
        $campaign = $this->pendingCampaignFor($teacher);

        $job = new GenerateHealthCampaign($campaign);

        try {
            $job->handle(app(HealthCampaignService::class));
            $this->fail('Esperava TextGenerationException.');
        } catch (TextGenerationException $e) {
            $job->failed($e);
        }

        $campaign->refresh();
        $this->assertSame(HealthCampaign::STATUS_FAILED, $campaign->status);
        $this->assertStringContainsString('quota exceeded', (string) $campaign->error_message);
        $this->assertNull($campaign->analysis);
    }

    public function test_students_cannot_access_health_campaigns(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)->get(route('health-campaigns.index'))->assertForbidden();
        $this->actingAs($student)->post(route('health-campaigns.store'), [])->assertForbidden();
    }
}
