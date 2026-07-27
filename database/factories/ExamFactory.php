<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Patient;
use App\Models\PatientHistory;
use App\Models\User;
use App\Service\ExamService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    protected $model = Exam::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'patient_id' => Patient::factory(),
            'patient_history_id' => PatientHistory::factory(),
            'exam_type_id' => ExamType::factory(),
            'sample_id' => null,
            'date' => now()->toDateString(),
            'observation' => null,
            'results' => null,
        ];
    }

    /**
     * 'status' não é mass assignable; é atribuído explicitamente.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Exam $exam): void {
            $exam->status ??= ExamService::STATUS_PENDING;
        });
    }
}
