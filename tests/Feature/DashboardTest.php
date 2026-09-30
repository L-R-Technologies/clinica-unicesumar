<?php

namespace Tests\Feature;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Calibration;
use App\Models\Exam;
use App\Models\ExamRejection;
use App\Models\ExamType;
use App\Models\Machine;
use App\Models\Patient;
use App\Models\Sample;
use App\Models\SampleType;
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
            ->assertJsonPath('props.teacherDashboard.approvalQueue.0.id', $stale->id)
            ->assertJsonPath('props.teacherDashboard.approvalQueue.0.student', 'Aluno B')
            ->assertJsonPath('props.teacherDashboard.approvalQueue.0.is_stale', true)
            ->assertJsonPath('props.teacherDashboard.staleApprovalsCount', 1)
            ->assertJsonPath('props.teacherDashboard.topExamTypes.0.label', 'Glicemia')
            ->assertJsonPath('props.teacherDashboard.topExamTypes.0.total', 2)
            ->assertJsonPath('props.teacherDashboard.activeStudents.0.name', 'Aluno A')
            ->assertJsonPath('props.teacherDashboard.activeStudents.0.total', 2)
            ->assertJsonCount(6, 'props.teacherDashboard.monthlyVolume');
    }

    public function test_teacher_dashboard_flags_overdue_samples_and_machines_without_recent_calibration(): void
    {
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->student()->create();
        $patient = Patient::factory()->create();
        $sampleType = SampleType::create(['name' => 'Sangue', 'description' => null]);

        $overdue = Sample::create([
            'patient_id' => $patient->id,
            'user_id' => $student->id,
            'sample_type_id' => $sampleType->id,
            'code' => 'AMO-000001',
            'date' => now()->subDays(10)->toDateString(),
            'status' => 'stored',
            'stored_at' => now()->subDays(10)->toDateString(),
        ]);
        Sample::create([
            'patient_id' => $patient->id,
            'user_id' => $student->id,
            'sample_type_id' => $sampleType->id,
            'code' => 'AMO-000002',
            'date' => now()->toDateString(),
            'status' => 'under review',
        ]);

        $calibrated = $this->createMachine('Espectrofotômetro', 'SN-001');
        $uncalibrated = $this->createMachine('Centrífuga', 'SN-002');
        Calibration::create([
            'user_id' => $teacher->id,
            'machine_id' => $calibrated->id,
            'calibration_date' => now()->subDays(5)->toDateString(),
            'value' => 5,
            'status' => 'approved',
        ]);

        $dashboard = $this->getDashboardAs($teacher)->assertOk()->json('props.teacherDashboard');

        $this->assertSame(1, $dashboard['stats'][5]['value']);
        $this->assertSame('warning', $dashboard['stats'][5]['tone']);
        $this->assertSame($overdue->id, $dashboard['samples']['overdueInFreezer'][0]['id']);
        $this->assertSame(10, $dashboard['samples']['overdueInFreezer'][0]['days']);
        $this->assertSame(1, $dashboard['samples']['distribution'][0]['count']);

        $this->assertSame($uncalibrated->id, $dashboard['machines'][0]['id']);
        $this->assertTrue($dashboard['machines'][0]['is_calibration_due']);
        $this->assertNull($dashboard['machines'][0]['last_calibration_at']);
        $this->assertSame($calibrated->id, $dashboard['machines'][1]['id']);
        $this->assertFalse($dashboard['machines'][1]['is_calibration_due']);
        $this->assertSame('approved', $dashboard['machines'][1]['last_calibration_status']);
        $this->assertSame(2, $dashboard['stats'][6]['value']);
    }

    private function createMachine(string $name, string $serialNumber): Machine
    {
        return Machine::create([
            'name' => $name,
            'model' => 'Modelo X',
            'serial_number' => $serialNumber,
            'location' => 'Laboratório 1',
            'calibration_range_min' => 0,
            'calibration_range_max' => 10,
            'status' => 'active',
        ]);
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
