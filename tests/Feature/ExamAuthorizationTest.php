<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Patient;
use App\Models\PatientHistory;
use App\Models\User;
use App\Service\ExamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ExamAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createExamForOwner(User $owner): Exam
    {
        $patient = Patient::factory()->create();
        $history = PatientHistory::factory()->create([
            'patient_id' => $patient->id,
            'user_id' => $owner->id,
        ]);
        $examType = ExamType::factory()->create();

        return Exam::factory()->create([
            'user_id' => $owner->id,
            'patient_id' => $patient->id,
            'patient_history_id' => $history->id,
            'exam_type_id' => $examType->id,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validUpdatePayload(Exam $exam): array
    {
        return [
            'patient_id' => $exam->patient_id,
            'patient_history_id' => $exam->patient_history_id,
            'exam_type_id' => $exam->exam_type_id,
            'date' => now()->toDateString(),
        ];
    }

    public function test_student_cannot_view_exam_of_another_user(): void
    {
        $owner = User::factory()->student()->create();
        $other = User::factory()->student()->create();
        $exam = $this->createExamForOwner($owner);

        $this->actingAs($other)
            ->get(route('exam.show', $exam->id))
            ->assertForbidden();
    }

    public function test_student_cannot_edit_exam_of_another_user(): void
    {
        $owner = User::factory()->student()->create();
        $other = User::factory()->student()->create();
        $exam = $this->createExamForOwner($owner);

        $this->actingAs($other)
            ->put(route('exam.update', $exam->id), $this->validUpdatePayload($exam))
            ->assertForbidden();
    }

    public function test_student_cannot_escalate_exam_status_via_update(): void
    {
        Mail::fake();
        Notification::fake();

        $owner = User::factory()->student()->create();
        $exam = $this->createExamForOwner($owner);

        $payload = $this->validUpdatePayload($exam);
        $payload['status'] = ExamService::STATUS_APPROVED;

        $this->actingAs($owner)
            ->put(route('exam.update', $exam->id), $payload)
            ->assertRedirect(route('exam.index'));

        $this->assertSame(ExamService::STATUS_PENDING, $exam->fresh()->status);
    }

    public function test_non_teacher_cannot_approve_exam(): void
    {
        Mail::fake();
        Notification::fake();

        $owner = User::factory()->student()->create();
        $exam = $this->createExamForOwner($owner);

        $this->actingAs($owner)
            ->post(route('exam.approve', $exam->id))
            ->assertForbidden();

        $this->assertSame(ExamService::STATUS_PENDING, $exam->fresh()->status);
    }

    public function test_non_teacher_cannot_reject_exam(): void
    {
        Mail::fake();
        Notification::fake();

        $owner = User::factory()->student()->create();
        $exam = $this->createExamForOwner($owner);

        $this->actingAs($owner)
            ->post(route('exam.reject', $exam->id), [
                'justification' => 'Justificativa suficientemente longa para o teste.',
            ])
            ->assertForbidden();

        $this->assertSame(ExamService::STATUS_PENDING, $exam->fresh()->status);
    }

    public function test_teacher_can_approve_exam(): void
    {
        Mail::fake();
        Notification::fake();

        $owner = User::factory()->student()->create();
        $teacher = User::factory()->teacher()->create();
        $exam = $this->createExamForOwner($owner);

        $this->actingAs($teacher)
            ->post(route('exam.approve', $exam->id))
            ->assertRedirect();

        $this->assertSame(ExamService::STATUS_APPROVED, $exam->fresh()->status);
    }
}
