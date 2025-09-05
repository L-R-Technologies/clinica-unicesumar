<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatientHistory;

class PatientHistoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primeiro histórico
        PatientHistory::create([
            'user_id' => 2, // médico/professor que preencheu
            'patient_id' => 1,
            'date' => now()->subDays(5),
            'fasting' => true,
            'fasting_hours' => 8,
            'alcohol_last_24h' => false,
            'on_medication' => true,
            'medications' => 'Atenolol 50mg',
            'on_supplements' => true,
            'supplements' => 'Vitamina D, Ômega 3',
            'chronic_disease' => true,
            'chronic_disease_details' => 'Hipertensão controlada',
            'recent_surgery' => false,
            'surgery_details' => null,
            'allergies' => true,
            'allergy_details' => 'Penicilina',
            'pregnant_or_lactating' => false,
            'smokes' => false,
            'cigarettes_per_day' => null,
            'physically_active' => true,
            'menstrual_period' => 'n/a',
            'recent_fever_or_flu' => false,
            'observation' => 'Paciente em boas condições gerais.',
        ]);

        // Segundo histórico
        PatientHistory::create([
            'user_id' => 3,
            'patient_id' => 2,
            'date' => now()->subDays(2),
            'fasting' => false,
            'fasting_hours' => null,
            'alcohol_last_24h' => true,
            'on_medication' => false,
            'medications' => null,
            'on_supplements' => false,
            'supplements' => null,
            'chronic_disease' => false,
            'chronic_disease_details' => null,
            'recent_surgery' => true,
            'surgery_details' => 'Cirurgia de apendicite há 1 mês',
            'allergies' => false,
            'allergy_details' => null,
            'pregnant_or_lactating' => false,
            'smokes' => true,
            'cigarettes_per_day' => 10,
            'physically_active' => false,
            'menstrual_period' => 'no',
            'recent_fever_or_flu' => true,
            'observation' => 'Paciente relata fadiga e falta de ar.',
        ]);
    }
}