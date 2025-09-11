<?php

namespace Database\Seeders;

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
            'type' => 'Hemograma Completo',
            'date' => now()->subDays(2),
            'results' => [
                'hemoglobina' => '14 g/dL',
                'hematocrito' => '42%',
                'plaquetas' => '250000 /µL',
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
            'type' => 'Urinálise',
            'date' => now()->subDay(),
            'results' => [
                'cor' => 'Amarelo claro',
                'aspecto' => 'Claro',
                'densidade' => '1.020',
            ],
            'status' => 'approved',
            'observation' => 'Paciente sem alterações significativas.',
            'justification_rejection' => null,
        ]);
    }
}
