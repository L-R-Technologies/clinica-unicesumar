<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeLabCapture;
use App\Models\ExamType;
use App\Models\LabCapture;
use App\Service\Ai\TextGenerationException;
use App\Service\LabCapture\LabCaptureAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LabCaptureAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_URL_PATTERN = 'generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.gemini.api_key', 'test-key');
        config()->set('services.gemini.model', 'gemini-test');
    }

    private function createApttExamType(): ExamType
    {
        $examType = ExamType::factory()->create(['name' => 'TTPA']);
        $examType->fields()->create(['name' => 'aptt_time', 'label' => 'Tempo', 'field_type' => 'float', 'unit' => 's']);
        $examType->fields()->create(['name' => 'aptt_ratio', 'label' => 'Relação', 'field_type' => 'float', 'unit' => null]);

        return $examType;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function fakeGemini(array $payload): void
    {
        Http::fake([
            self::GEMINI_URL_PATTERN => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => json_encode($payload, JSON_UNESCAPED_UNICODE)]], 'role' => 'model']],
                ],
            ]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function aiPayload(?int $examTypeId): array
    {
        return [
            'exam_type_id' => $examTypeId,
            'exam_type_confidence' => 0.93,
            'exam_type_reasoning' => 'A impressão traz "EXAME: TTPA".',
            'suggested_exam_type_name' => 'TTPA',
            'results' => [
                ['field_name' => 'aptt_time', 'value' => '16,9', 'source_label' => 'TEMPO', 'source_value' => '16.9s'],
                ['field_name' => 'campo_inventado', 'value' => '1', 'source_label' => 'X', 'source_value' => '1'],
            ],
            'unmapped_values' => [['label' => 'CANAL', 'value' => '1']],
            'metadata' => [
                'exam_datetime' => '2025-03-20 16:14:06',
                'machine_serial' => '21050207',
                'sequence_number' => '0195',
                'channel' => '1',
                'operator' => 'OPERADOR',
                'status_note' => null,
            ],
        ];
    }

    public function test_job_maps_results_to_known_fields_and_marks_capture_analyzed(): void
    {
        $examType = $this->createApttExamType();
        $this->fakeGemini($this->aiPayload($examType->id));
        $capture = LabCapture::factory()->create();

        (new AnalyzeLabCapture($capture))->handle(app(LabCaptureAnalysisService::class));

        $capture->refresh();
        $this->assertSame(LabCapture::STATUS_ANALYZED, $capture->status);
        $this->assertSame('gemini-test', $capture->ai_model);
        $this->assertSame($examType->id, $capture->analysis['exam_type_id']);
        $this->assertSame(['aptt_time' => 16.9], $capture->analysis['results']);
        $this->assertCount(1, $capture->analysis['results_details']);
        $this->assertSame('21050207', $capture->analysis['metadata']['machine_serial']);
        $this->assertSame([['label' => 'CANAL', 'value' => '1']], $capture->analysis['unmapped_values']);

        Http::assertSent(function (Request $request) use ($examType): bool {
            $prompt = json_encode($request->data());

            return str_contains($prompt, "ID {$examType->id}: TTPA")
                && str_contains($prompt, 'aptt_time')
                && str_contains($prompt, 'TEMPO:');
        });
    }

    public function test_unknown_exam_type_marks_capture_unrecognized(): void
    {
        $this->createApttExamType();
        $this->fakeGemini($this->aiPayload(null));
        $capture = LabCapture::factory()->create();

        (new AnalyzeLabCapture($capture))->handle(app(LabCaptureAnalysisService::class));

        $capture->refresh();
        $this->assertSame(LabCapture::STATUS_UNRECOGNIZED, $capture->status);
        $this->assertNull($capture->analysis['exam_type_id']);
        $this->assertSame('TTPA', $capture->analysis['suggested_exam_type_name']);
        $this->assertSame([], $capture->analysis['results']);
    }

    public function test_exam_type_id_outside_catalog_is_discarded(): void
    {
        $this->createApttExamType();
        $this->fakeGemini($this->aiPayload(9999));
        $capture = LabCapture::factory()->create();

        (new AnalyzeLabCapture($capture))->handle(app(LabCaptureAnalysisService::class));

        $this->assertSame(LabCapture::STATUS_UNRECOGNIZED, $capture->fresh()->status);
    }

    public function test_patient_identification_is_masked_before_reaching_the_ai(): void
    {
        $examType = $this->createApttExamType();
        $this->fakeGemini($this->aiPayload($examType->id));
        $capture = LabCapture::factory()->create([
            'raw_text' => "NOME: Maria Segredo Silva\nID(12345)\nEXAME: TTPA\nTEMPO: 16.9s",
            'raw_hash' => hash('sha256', 'masked'),
        ]);

        (new AnalyzeLabCapture($capture))->handle(app(LabCaptureAnalysisService::class));

        Http::assertSent(function (Request $request): bool {
            $prompt = json_encode($request->data(), JSON_UNESCAPED_UNICODE);

            return ! str_contains($prompt, 'Maria Segredo Silva')
                && ! str_contains($prompt, '12345')
                && str_contains($prompt, '[omitido]')
                && str_contains($prompt, 'TEMPO: 16.9s');
        });

        $this->assertStringContainsString('Maria Segredo Silva', $capture->fresh()->raw_text);
    }

    public function test_provider_failure_marks_capture_failed(): void
    {
        $this->createApttExamType();
        Http::fake([self::GEMINI_URL_PATTERN => Http::response(['error' => ['message' => 'quota exceeded']], 429)]);
        $capture = LabCapture::factory()->create();
        $job = new AnalyzeLabCapture($capture);

        try {
            $job->handle(app(LabCaptureAnalysisService::class));
            $this->fail('Esperava TextGenerationException.');
        } catch (TextGenerationException $e) {
            $job->failed($e);
        }

        $capture->refresh();
        $this->assertSame(LabCapture::STATUS_FAILED, $capture->status);
        $this->assertStringContainsString('quota exceeded', (string) $capture->error_message);
    }
}
