<?php

namespace Tests\Feature;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Patient;
use App\Models\User;
use App\Service\ExamReferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamReferenceEvaluationTest extends TestCase
{
    use RefreshDatabase;

    private const EXAM_DATE = '2026-09-01';

    private function createGlucoseExam(string $sex, string $birthday, float $result): Exam
    {
        $patient = Patient::factory()->create(['sex' => $sex, 'birthday' => $birthday]);
        $examType = ExamType::factory()->create();
        $field = $examType->fields()->create([
            'name' => 'glucose',
            'label' => 'Glicose',
            'field_type' => 'float',
            'unit' => 'mg/dL',
        ]);
        $field->references()->create(['min_value' => 70, 'max_value' => 99]);
        $field->references()->create(['sex' => 'female', 'age_min' => 18, 'age_max' => 59, 'min_value' => 65, 'max_value' => 90]);
        $field->references()->create(['age_max' => 17, 'min_value' => 60, 'max_value' => 100]);

        return Exam::factory()->create([
            'patient_id' => $patient->id,
            'exam_type_id' => $examType->id,
            'date' => self::EXAM_DATE,
            'results' => ['glucose' => $result],
        ]);
    }

    public function test_most_specific_reference_is_chosen_for_adult_woman(): void
    {
        $exam = $this->createGlucoseExam('female', '1990-05-10', 95);

        $evaluated = app(ExamReferenceService::class)->evaluateResults($exam);

        $this->assertSame('65 a 90', $evaluated['glucose']['range_label']);
        $this->assertSame('Feminino, 18 a 59 anos', $evaluated['glucose']['criteria_label']);
        $this->assertSame(ExamReferenceService::STATUS_ABOVE, $evaluated['glucose']['status']);
        $this->assertSame('Acima', $evaluated['glucose']['status_label']);
    }

    public function test_generic_reference_is_used_when_no_specific_one_matches(): void
    {
        $exam = $this->createGlucoseExam('male', '1980-01-01', 95);

        $evaluated = app(ExamReferenceService::class)->evaluateResults($exam);

        $this->assertSame('70 a 99', $evaluated['glucose']['range_label']);
        $this->assertSame(ExamReferenceService::STATUS_WITHIN, $evaluated['glucose']['status']);
    }

    public function test_age_based_reference_is_used_for_child(): void
    {
        $exam = $this->createGlucoseExam('female', '2015-03-01', 55);

        $evaluated = app(ExamReferenceService::class)->evaluateResults($exam);

        $this->assertSame('Ambos os sexos, até 17 anos', $evaluated['glucose']['criteria_label']);
        $this->assertSame(ExamReferenceService::STATUS_BELOW, $evaluated['glucose']['status']);
    }

    public function test_reference_without_result_has_no_status(): void
    {
        $exam = $this->createGlucoseExam('male', '1980-01-01', 95);
        $exam->update(['results' => null]);

        $evaluated = app(ExamReferenceService::class)->evaluateResults($exam->fresh());

        $this->assertSame('70 a 99', $evaluated['glucose']['range_label']);
        $this->assertNull($evaluated['glucose']['status']);
    }

    public function test_exam_pdf_renders_with_reference_values(): void
    {
        $teacher = User::factory()->teacher()->create();
        $exam = $this->createGlucoseExam('female', '1990-05-10', 95);

        $this->actingAs($teacher)
            ->get(route('exam.export-pdf', $exam->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_exam_details_expose_result_references(): void
    {
        $teacher = User::factory()->teacher()->create();
        $exam = $this->createGlucoseExam('female', '1990-05-10', 95);
        $inertiaVersion = app(HandleInertiaRequests::class)->version(request()) ?? '';

        $this->actingAs($teacher)
            ->withHeaders(['X-Inertia' => 'true', 'X-Inertia-Version' => $inertiaVersion])
            ->get(route('exam.show', $exam->id))
            ->assertOk()
            ->assertJsonPath('props.resultReferences.glucose.status', ExamReferenceService::STATUS_ABOVE)
            ->assertJsonPath('props.resultReferences.glucose.range_label', '65 a 90');
    }
}
