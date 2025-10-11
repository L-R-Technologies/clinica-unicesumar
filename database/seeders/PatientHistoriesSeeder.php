<?php

namespace Database\Seeders;

use App\Models\PatientHistory;
use Illuminate\Database\Seeder;

class PatientHistoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patientHistories = [
            // Histórico da Paciente 1 (Maria Silva Santos)
            [
                'user_id' => 2, // Professor que preencheu
                'patient_id' => 1,
                'fasting' => true,
                'fasting_hours' => 8,
                'alcohol_last_24h' => false,
                'on_medication' => true,
                'medications' => 'Atenolol 50mg, Hidroclorotiazida 25mg',
                'on_supplements' => true,
                'supplements' => 'Vitamina D 2000UI, Ômega 3 1g',
                'chronic_disease' => true,
                'chronic_disease_details' => 'Hipertensão arterial sistêmica controlada',
                'infectious_disease_history' => false,
                'infectious_disease_details' => null,
                'recent_surgery' => false,
                'surgery_details' => null,
                'allergies' => true,
                'allergy_details' => 'Penicilina - reação cutânea',
                'pregnant_or_lactating' => false,
                'smokes' => false,
                'cigarettes_per_day' => null,
                'physically_active' => true,
                'menstrual_period' => 'n/a',
                'recent_fever_or_flu' => false,
                'observation' => 'Paciente em boas condições gerais. Pressão arterial controlada.',
                'recorded_at' => now()->subDays(5),
            ],

            // Segundo histórico da Paciente 1
            [
                'user_id' => 3, // Aluno 1 que preencheu
                'patient_id' => 1,
                'fasting' => false,
                'fasting_hours' => null,
                'alcohol_last_24h' => false,
                'on_medication' => true,
                'medications' => 'Atenolol 50mg, Hidroclorotiazida 25mg',
                'on_supplements' => true,
                'supplements' => 'Vitamina D 2000UI, Ômega 3 1g',
                'chronic_disease' => true,
                'chronic_disease_details' => 'Hipertensão arterial sistêmica controlada',
                'infectious_disease_history' => false,
                'infectious_disease_details' => null,
                'recent_surgery' => false,
                'surgery_details' => null,
                'allergies' => true,
                'allergy_details' => 'Penicilina - reação cutânea',
                'pregnant_or_lactating' => false,
                'smokes' => false,
                'cigarettes_per_day' => null,
                'physically_active' => true,
                'menstrual_period' => 'n/a',
                'recent_fever_or_flu' => false,
                'observation' => 'Retorno para acompanhamento. Paciente assintomática.',
                'recorded_at' => now()->subDays(2),
            ],

            // Histórico do Paciente 2 (João Carlos Oliveira)
            [
                'user_id' => 2, // Professor que preencheu
                'patient_id' => 2,
                'fasting' => true,
                'fasting_hours' => 12,
                'alcohol_last_24h' => true,
                'on_medication' => false,
                'medications' => null,
                'on_supplements' => false,
                'supplements' => null,
                'chronic_disease' => false,
                'chronic_disease_details' => null,
                'infectious_disease_history' => true,
                'infectious_disease_details' => 'COVID-19 em março de 2023',
                'recent_surgery' => true,
                'surgery_details' => 'Apendicectomia laparoscópica há 2 meses',
                'allergies' => false,
                'allergy_details' => null,
                'pregnant_or_lactating' => false,
                'smokes' => true,
                'cigarettes_per_day' => 10,
                'physically_active' => false,
                'menstrual_period' => 'n/a',
                'recent_fever_or_flu' => true,
                'observation' => 'Paciente relata fadiga e episódios de falta de ar. Tabagista.',
                'recorded_at' => now()->subDays(1),
            ],
        ];

        foreach ($patientHistories as $history) {
            PatientHistory::create($history);
        }
    }
}
