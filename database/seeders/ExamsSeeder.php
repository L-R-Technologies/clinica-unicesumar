<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('exams')->insert([
            [
                'user_id' => 2, // profissional responsável
                'patient_history_id' => 1,
                'patient_id' => 1,
                'sample_id' => 1,
                'type' => 'Hemograma Completo',
                'date' => now()->subDays(2),
                'results' => json_encode([
                    'hemoglobina' => '14 g/dL',
                    'hematocrito' => '42%',
                    'plaquetas' => '250000 /µL',
                ]),
                'status' => 'pending_approval',
                'observation' => 'Amostra coletada corretamente.',
                'justification_rejection' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'patient_history_id' => 2,
                'patient_id' => 2,
                'sample_id' => 2,
                'type' => 'Urinálise',
                'date' => now()->subDay(),
                'results' => json_encode([
                    'cor' => 'Amarelo claro',
                    'aspecto' => 'Claro',
                    'densidade' => '1.020',
                ]),
                'status' => 'approved',
                'observation' => 'Paciente sem alterações significativas.',
                'justification_rejection' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
