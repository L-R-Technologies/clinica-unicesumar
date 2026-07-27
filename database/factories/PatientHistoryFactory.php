<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientHistory>
 */
class PatientHistoryFactory extends Factory
{
    protected $model = PatientHistory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'patient_id' => Patient::factory(),
            'recorded_at' => now(),
            'fasting' => false,
            'alcohol_last_24h' => false,
            'on_medication' => false,
            'on_supplements' => false,
            'chronic_disease' => false,
            'infectious_disease_history' => false,
            'recent_surgery' => false,
            'allergies' => false,
            'pregnant_or_lactating' => false,
            'smokes' => false,
            'physically_active' => true,
            'menstrual_period' => 'n/a',
            'recent_fever_or_flu' => false,
        ];
    }
}
