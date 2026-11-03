<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeLabCapture;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\LabCapture;
use App\Models\Patient;
use App\Models\PatientHistory;
use App\Models\User;
use App\Service\ExamService;
use App\Service\LabCapture\LabCaptureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LabCaptureReviewTest extends TestCase
{
    use RefreshDatabase;

    private ExamType $examType;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Notification::fake();

        $this->examType = ExamType::factory()->create(['name' => 'TTPA']);
        $this->examType->fields()->create(['name' => 'aptt_time', 'label' => 'Tempo', 'field_type' => 'float', 'unit' => 's']);
        $this->examType->fields()->create(['name' => 'aptt_ratio', 'label' => 'Relação', 'field_type' => 'float', 'unit' => null]);
    }

    private function analyzedCapture(?int $examTypeId = null): LabCapture
    {
        return LabCapture::factory()->analyzed([
            'exam_type_id' => $examTypeId ?? $this->examType->id,
            'exam_type_confidence' => 0.9,
            'exam_type_reasoning' => 'EXAME: TTPA',
            'suggested_exam_type_name' => 'TTPA',
            'results' => ['aptt_time' => 16.9],
            'results_details' => [],
            'unmapped_values' => [],
            'metadata' => ['exam_datetime' => '2025-03-20 16:14:06', 'machine_serial' => null, 'sequence_number' => null, 'channel' => null, 'operator' => null, 'status_note' => null],
        ])->create();
    }

    /**
     * @return array{patient: Patient, history: PatientHistory}
     */
    private function patientWithHistory(User $owner): array
    {
        $patient = Patient::factory()->create();
        $history = PatientHistory::factory()->create(['patient_id' => $patient->id, 'user_id' => $owner->id]);

        return ['patient' => $patient, 'history' => $history];
    }

    private function pendingExamFor(User $owner, Patient $patient, PatientHistory $history, ?ExamType $examType = null): Exam
    {
        return Exam::factory()->create([
            'user_id' => $owner->id,
            'patient_id' => $patient->id,
            'patient_history_id' => $history->id,
            'exam_type_id' => ($examType ?? $this->examType)->id,
        ]);
    }

    public function test_student_can_list_and_view_captures(): void
    {
        $student = User::factory()->student()->create();
        $capture = $this->analyzedCapture();

        $this->actingAs($student)->get(route('lab-captures.index'))->assertOk();
        $this->actingAs($student)->get(route('lab-captures.show', $capture->id))->assertOk();
    }

    public function test_patient_cannot_access_captures(): void
    {
        $patient = User::factory()->patient()->create();
        $capture = $this->analyzedCapture();

        $this->actingAs($patient)->get(route('lab-captures.index'))->assertForbidden();
        $this->actingAs($patient)->get(route('lab-captures.show', $capture->id))->assertForbidden();
    }

    public function test_student_links_capture_to_own_pending_exam_with_edited_results(): void
    {
        $student = User::factory()->student()->create();
        ['patient' => $patient, 'history' => $history] = $this->patientWithHistory($student);
        $exam = $this->pendingExamFor($student, $patient, $history);
        $capture = $this->analyzedCapture();

        $this->actingAs($student)
            ->post(route('lab-captures.link', $capture->id), [
                'mode' => LabCaptureService::LINK_MODE_EXISTING,
                'exam_type_id' => $this->examType->id,
                'exam_id' => $exam->id,
                'results' => ['aptt_time' => '17,2', 'aptt_ratio' => '', 'unknown' => 'x'],
            ])
            ->assertRedirect(route('exam.show', $exam->id))
            ->assertSessionHas('success');

        $exam->refresh();
        $this->assertSame(['aptt_time' => 17.2], $exam->results);
        $this->assertSame(ExamService::STATUS_PENDING_APPROVAL, $exam->status);

        $capture->refresh();
        $this->assertSame(LabCapture::STATUS_LINKED, $capture->status);
        $this->assertSame($exam->id, $capture->exam_id);
        $this->assertSame($student->id, $capture->reviewed_by);
        $this->assertSame(['aptt_time' => 17.2], $capture->analysis['confirmed_results']);
    }

    public function test_student_cannot_link_capture_to_another_students_exam(): void
    {
        $owner = User::factory()->student()->create();
        $other = User::factory()->student()->create();
        ['patient' => $patient, 'history' => $history] = $this->patientWithHistory($owner);
        $exam = $this->pendingExamFor($owner, $patient, $history);
        $capture = $this->analyzedCapture();

        $this->actingAs($other)
            ->post(route('lab-captures.link', $capture->id), [
                'mode' => LabCaptureService::LINK_MODE_EXISTING,
                'exam_type_id' => $this->examType->id,
                'exam_id' => $exam->id,
                'results' => ['aptt_time' => '17.2'],
            ])
            ->assertForbidden();

        $this->assertSame(LabCapture::STATUS_ANALYZED, $capture->fresh()->status);
        $this->assertNull($exam->fresh()->results);
    }

    public function test_linking_to_exam_of_another_type_is_rejected(): void
    {
        $teacher = User::factory()->teacher()->create();
        ['patient' => $patient, 'history' => $history] = $this->patientWithHistory($teacher);
        $otherType = ExamType::factory()->create(['name' => 'Glicemia']);
        $exam = $this->pendingExamFor($teacher, $patient, $history, $otherType);
        $capture = $this->analyzedCapture();

        $this->actingAs($teacher)
            ->from(route('lab-captures.show', $capture->id))
            ->post(route('lab-captures.link', $capture->id), [
                'mode' => LabCaptureService::LINK_MODE_EXISTING,
                'exam_type_id' => $this->examType->id,
                'exam_id' => $exam->id,
                'results' => ['aptt_time' => '17.2'],
            ])
            ->assertRedirect(route('lab-captures.show', $capture->id))
            ->assertSessionHasErrors('exam_id');

        $this->assertSame(LabCapture::STATUS_ANALYZED, $capture->fresh()->status);
    }

    public function test_teacher_creates_new_exam_from_capture(): void
    {
        $teacher = User::factory()->teacher()->create();
        ['patient' => $patient, 'history' => $history] = $this->patientWithHistory($teacher);
        $capture = $this->analyzedCapture();

        $this->actingAs($teacher)
            ->post(route('lab-captures.link', $capture->id), [
                'mode' => LabCaptureService::LINK_MODE_NEW,
                'exam_type_id' => $this->examType->id,
                'patient_id' => $patient->id,
                'patient_history_id' => $history->id,
                'date' => '2025-03-20',
                'observation' => 'Importado do coagulômetro.',
                'results' => ['aptt_time' => '16.9', 'aptt_ratio' => '1.05'],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $exam = Exam::firstOrFail();
        $this->assertSame($teacher->id, $exam->user_id);
        $this->assertSame($this->examType->id, $exam->exam_type_id);
        $this->assertSame(['aptt_time' => 16.9, 'aptt_ratio' => 1.05], $exam->results);
        $this->assertSame(ExamService::STATUS_PENDING, $exam->status);
        $this->assertSame($exam->id, $capture->fresh()->exam_id);
    }

    public function test_new_exam_mode_requires_patient_history_and_date(): void
    {
        $teacher = User::factory()->teacher()->create();
        $capture = $this->analyzedCapture();

        $this->actingAs($teacher)
            ->from(route('lab-captures.show', $capture->id))
            ->post(route('lab-captures.link', $capture->id), [
                'mode' => LabCaptureService::LINK_MODE_NEW,
                'exam_type_id' => $this->examType->id,
            ])
            ->assertSessionHasErrors(['patient_id', 'patient_history_id', 'date']);
    }

    public function test_unrecognized_capture_can_be_linked_with_a_manually_chosen_type(): void
    {
        $teacher = User::factory()->teacher()->create();
        ['patient' => $patient, 'history' => $history] = $this->patientWithHistory($teacher);
        $capture = LabCapture::factory()->analyzed([
            'exam_type_id' => null,
            'exam_type_confidence' => 0.2,
            'exam_type_reasoning' => 'Nenhum tipo corresponde.',
            'suggested_exam_type_name' => 'TTPA',
            'results' => [],
            'results_details' => [],
            'unmapped_values' => [['label' => 'TEMPO', 'value' => '16.9s']],
            'metadata' => [],
        ])->create();

        $this->assertSame(LabCapture::STATUS_UNRECOGNIZED, $capture->status);

        $this->actingAs($teacher)
            ->post(route('lab-captures.link', $capture->id), [
                'mode' => LabCaptureService::LINK_MODE_NEW,
                'exam_type_id' => $this->examType->id,
                'patient_id' => $patient->id,
                'patient_history_id' => $history->id,
                'date' => '2025-03-20',
                'results' => ['aptt_time' => '16.9s'],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(['aptt_time' => 16.9], Exam::firstOrFail()->results);
        $this->assertSame(LabCapture::STATUS_LINKED, $capture->fresh()->status);
    }

    public function test_linked_capture_cannot_be_reviewed_again(): void
    {
        $teacher = User::factory()->teacher()->create();
        $capture = $this->analyzedCapture();
        $capture->update(['status' => LabCapture::STATUS_LINKED]);

        $this->actingAs($teacher)
            ->post(route('lab-captures.link', $capture->id), ['mode' => LabCaptureService::LINK_MODE_NEW])
            ->assertForbidden();
    }

    public function test_failed_capture_can_be_retried_and_queues_the_job(): void
    {
        Queue::fake();
        $student = User::factory()->student()->create();
        $capture = $this->analyzedCapture();
        $capture->update(['status' => LabCapture::STATUS_FAILED, 'error_message' => 'quota exceeded']);

        $this->actingAs($student)
            ->post(route('lab-captures.retry', $capture->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $capture->refresh();
        $this->assertSame(LabCapture::STATUS_RECEIVED, $capture->status);
        $this->assertNull($capture->error_message);
        Queue::assertPushed(AnalyzeLabCapture::class);
    }

    public function test_only_teacher_can_discard_capture(): void
    {
        $student = User::factory()->student()->create();
        $teacher = User::factory()->teacher()->create();
        $capture = $this->analyzedCapture();

        $this->actingAs($student)->delete(route('lab-captures.discard', $capture->id))->assertForbidden();

        $this->actingAs($teacher)
            ->delete(route('lab-captures.discard', $capture->id))
            ->assertRedirect(route('lab-captures.index'));

        $this->assertSame(LabCapture::STATUS_DISCARDED, $capture->fresh()->status);
    }

    public function test_linkable_exams_are_filtered_by_patient_type_status_and_owner(): void
    {
        $student = User::factory()->student()->create();
        $other = User::factory()->student()->create();
        ['patient' => $patient, 'history' => $history] = $this->patientWithHistory($student);
        $own = $this->pendingExamFor($student, $patient, $history);
        $this->pendingExamFor($other, $patient, $history);
        $approved = $this->pendingExamFor($student, $patient, $history);
        $approved->status = ExamService::STATUS_APPROVED;
        $approved->save();

        $service = app(LabCaptureService::class);

        $this->assertSame([$own->id], $service->getLinkableExams($student, $patient->id, $this->examType->id)->pluck('id')->all());
        $this->assertCount(2, $service->getLinkableExams(User::factory()->teacher()->create(), $patient->id, $this->examType->id));
    }
}
