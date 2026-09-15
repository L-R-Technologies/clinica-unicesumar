<?php

namespace Tests\Feature;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Exam;
use App\Models\ExamRejection;
use App\Models\ExamType;
use App\Models\User;
use App\Service\ExamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function createExamWithStatus(User $student, string $status, ?ExamType $examType = null): Exam
    {
        $exam = Exam::factory()->create([
            'user_id' => $student->id,
            'exam_type_id' => $examType?->id ?? ExamType::factory(),
            'date' => now()->toDateString(),
        ]);
        $exam->status = $status;
        $exam->save();

        return $exam;
    }

    private function getDashboardAs(User $user): TestResponse
    {
        $inertiaVersion = app(HandleInertiaRequests::class)->version(request()) ?? '';

        return $this->actingAs($user)
            ->withHeaders(['X-Inertia' => 'true', 'X-Inertia-Version' => $inertiaVersion])
            ->get(route('home'));
    }

    public function test_teacher_dashboard_covers_all_exams_in_the_system(): void
    {
        $teacher = User::factory()->teacher()->create();
        $studentA = User::factory()->student()->create(['name' => 'Aluno A']);
        $studentB = User::factory()->student()->create(['name' => 'Aluno B']);
        $glucose = ExamType::factory()->create(['name' => 'Glicemia']);

        $this->createExamWithStatus($studentA, ExamService::STATUS_APPROVED, $glucose);
        $rejected = $this->createExamWithStatus($studentA, ExamService::STATUS_REJECTED, $glucose);
        ExamRejection::create(['exam_id' => $rejected->id, 'user_id' => $teacher->id, 'justification' => 'Amostra insuficiente para análise.']);

        $stale = $this->createExamWithStatus($studentB, ExamService::STATUS_PENDING_APPROVAL);
        Exam::whereKey($stale->id)->update(['updated_at' => now()->subDays(3)]);

        $this->getDashboardAs($teacher)
            ->assertOk()
            ->assertJsonPath('props.studentDashboard', null)
            ->assertJsonPath('props.teacherDashboard.stats.1.value', 1)
            ->assertJsonPath('props.teacherDashboard.stats.2.value', 50)
            ->assertJsonPath('props.teacherDashboard.statusDistribution.0.count', 1)
            ->assertJsonPath('props.teacherDashboard.rejectionRateByExamType.0.label', 'Glicemia')
            ->assertJsonPath('props.teacherDashboard.rejectionRateByExamType.0.rate', 50)
            ->assertJsonPath('props.teacherDashboard.rejectionRanking.0.name', 'Aluno A')
            ->assertJsonPath('props.teacherDashboard.staleApprovals.0.id', $stale->id)
            ->assertJsonPath('props.teacherDashboard.staleApprovals.0.student', 'Aluno B')
            ->assertJsonCount(6, 'props.teacherDashboard.monthlyVolume');
    }

    public function test_teacher_dashboard_lists_students_without_recent_exams(): void
    {
        $teacher = User::factory()->teacher()->create();
        $active = User::factory()->student()->create(['name' => 'Aluno Ativo']);
        User::factory()->student()->create(['name' => 'Aluno Parado']);
        $this->createExamWithStatus($active, ExamService::STATUS_PENDING);

        $inactiveStudents = $this->getDashboardAs($teacher)
            ->assertOk()
            ->json('props.teacherDashboard.inactiveStudents');

        $this->assertContains('Aluno Parado', $inactiveStudents);
        $this->assertNotContains('Aluno Ativo', $inactiveStudents);
    }

    public function test_student_dashboard_only_covers_own_exams(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->student()->create();
        $otherStudent = User::factory()->student()->create();

        $this->createExamWithStatus($student, ExamService::STATUS_APPROVED);
        $rejected = $this->createExamWithStatus($student, ExamService::STATUS_REJECTED);
        ExamRejection::create(['exam_id' => $rejected->id, 'user_id' => $teacher->id, 'justification' => 'Tubo inadequado.']);
        $this->createExamWithStatus($otherStudent, ExamService::STATUS_APPROVED);
        $this->createExamWithStatus($otherStudent, ExamService::STATUS_PENDING);

        $this->getDashboardAs($student)
            ->assertOk()
            ->assertJsonPath('props.teacherDashboard', null)
            ->assertJsonPath('props.studentDashboard.stats.0.value', 2)
            ->assertJsonPath('props.studentDashboard.stats.2.value', 1)
            ->assertJsonPath('props.studentDashboard.stats.3.value', 50)
            ->assertJsonCount(1, 'props.studentDashboard.actionRequired')
            ->assertJsonPath('props.studentDashboard.actionRequired.0.id', $rejected->id)
            ->assertJsonPath('props.studentDashboard.recentRejections.0.justification', 'Tubo inadequado.');
    }

    public function test_patient_sees_no_dashboard_data(): void
    {
        $patient = User::factory()->patient()->create();

        $this->getDashboardAs($patient)
            ->assertOk()
            ->assertJsonPath('props.teacherDashboard', null)
            ->assertJsonPath('props.studentDashboard', null);
    }
}
