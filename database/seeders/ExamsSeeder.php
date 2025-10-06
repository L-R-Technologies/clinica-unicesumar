<?php

namespace Database\Seeders;

use App\Enums\ExamType;
use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam::create([
            'user_id' => 2,
            'patient_history_id' => 1,
            'patient_id' => 1,
            'sample_id' => 1,
            'type' => ExamType::COMPLETE_BLOOD_COUNT->value,
            'date' => now()->subDays(2),
            'results' => [
                'red_blood_cells' => '4.5 million/mm³',
                'hemoglobin' => '14 g/dL',
                'hematocrit' => '42%',
                'white_blood_cells' => '7200 /µL',
                'platelets' => '250000 /µL',
            ],
            'status' => 'pending_approval',
            'observation' => 'Amostra coletada corretamente.',
            'justification_rejection' => null,
        ]);

        Exam::create([
            'user_id' => 3,
            'patient_history_id' => 2,
            'patient_id' => 1,
            'sample_id' => 2,
            'type' => ExamType::URINALYSIS->value,
            'date' => now()->subDay(),
            'results' => [
                'volume' => '50 mL',
                'color' => 'Amarelo claro',
                'aspect' => 'Límpido',
                'density' => '1.020',
                'ph' => '6.0',
                'protein' => 'Negativo',
                'glucose' => 'Negativo',
            ],
            'status' => 'approved',
            'observation' => 'Paciente sem alterações significativas.',
            'justification_rejection' => null,
        ]);

        Exam::create([
            'user_id' => 2,
            'patient_history_id' => 1,
            'patient_id' => 1,
            'sample_id' => 3,
            'type' => ExamType::GLUCOSE->value,
            'date' => now(),
            'results' => [
                'glucose' => '85 mg/dL',
            ],
            'status' => 'pending',
            'observation' => 'Glicemia de jejum.',
            'justification_rejection' => null,
        ]);
    }
}
