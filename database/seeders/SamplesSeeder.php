<?php

namespace Database\Seeders;

use App\Models\Sample;
use Illuminate\Database\Seeder;

class SamplesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $samples = [
            // Amostras da Paciente 1 (Maria Silva Santos)
            [
                'patient_id' => 1,
                'user_id' => 2, // Professor
                'sample_type_id' => 1, // Sangue Total
                'code' => 'SMP000001',
                'date' => now()->subDays(5),
                'location' => 'Laboratório Central',
                'status' => 'stored',
                'notified' => true,
            ],
            [
                'patient_id' => 1,
                'user_id' => 3, // Aluno 1
                'sample_type_id' => 2, // Soro
                'code' => 'SMP000002',
                'date' => now()->subDays(5),
                'location' => 'Laboratório Central',
                'status' => 'stored',
                'notified' => true,
            ],
            [
                'patient_id' => 1,
                'user_id' => 4, // Aluno 2
                'sample_type_id' => 4, // Urina
                'code' => 'SMP000003',
                'date' => now()->subDays(2),
                'location' => 'Laboratório Norte',
                'status' => 'stored',
                'notified' => true,
            ],

            // Amostras do Paciente 2 (João Carlos Oliveira)
            [
                'patient_id' => 2,
                'user_id' => 2, // Professor
                'sample_type_id' => 1, // Sangue Total
                'code' => 'SMP000004',
                'date' => now()->subDays(1),
                'location' => 'Laboratório Central',
                'status' => 'under review',
                'notified' => false,
            ],
            [
                'patient_id' => 2,
                'user_id' => 3, // Aluno 1
                'sample_type_id' => 3, // Plasma
                'code' => 'SMP000005',
                'date' => now()->subDays(1),
                'location' => 'Laboratório Central',
                'status' => 'under review',
                'notified' => false,
            ],
            [
                'patient_id' => 2,
                'user_id' => 4, // Aluno 2
                'sample_type_id' => 5, // Fezes
                'code' => 'SMP000006',
                'date' => now()->subDays(1),
                'location' => 'Laboratório de Parasitologia',
                'status' => 'under review',
                'notified' => false,
            ],
            [
                'patient_id' => 2,
                'user_id' => 2, // Professor
                'sample_type_id' => 4, // Urina
                'code' => 'SMP000007',
                'date' => now()->subDays(1),
                'location' => 'Laboratório Norte',
                'status' => 'stored',
                'notified' => true,
            ],

            // Amostras da Paciente 3 (Ana Paula Costa)
            [
                'patient_id' => 3,
                'user_id' => 3, // Aluno 1
                'sample_type_id' => 1, // Sangue Total
                'code' => 'SMP000008',
                'date' => now()->subDays(3),
                'location' => 'Laboratório Central',
                'status' => 'stored',
                'notified' => true,
            ],
            [
                'patient_id' => 3,
                'user_id' => 4, // Aluno 2
                'sample_type_id' => 2, // Soro
                'code' => 'SMP000009',
                'date' => now()->subDays(3),
                'location' => 'Laboratório Central',
                'status' => 'stored',
                'notified' => true,
            ],

            // Amostras do Paciente 4 (Carlos Eduardo Ferreira)
            [
                'patient_id' => 4,
                'user_id' => 2, // Professor
                'sample_type_id' => 1, // Sangue Total
                'code' => 'SMP000010',
                'date' => now()->subDays(4),
                'location' => 'Laboratório Central',
                'status' => 'stored',
                'notified' => true,
            ],
            [
                'patient_id' => 4,
                'user_id' => 3, // Aluno 1
                'sample_type_id' => 2, // Soro
                'code' => 'SMP000011',
                'date' => now()->subDays(4),
                'location' => 'Laboratório Central',
                'status' => 'stored',
                'notified' => true,
            ],
            [
                'patient_id' => 4,
                'user_id' => 4, // Aluno 2
                'sample_type_id' => 4, // Urina
                'code' => 'SMP000012',
                'date' => now()->subDays(4),
                'location' => 'Laboratório Norte',
                'status' => 'stored',
                'notified' => true,
            ],

            // Amostras da Paciente 5 (Fernanda Lima Silva)
            [
                'patient_id' => 5,
                'user_id' => 2, // Professor
                'sample_type_id' => 1, // Sangue Total
                'code' => 'SMP000013',
                'date' => now()->subDays(6),
                'location' => 'Laboratório Central',
                'status' => 'stored',
                'notified' => true,
            ],
            [
                'patient_id' => 5,
                'user_id' => 3, // Aluno 1
                'sample_type_id' => 2, // Soro
                'code' => 'SMP000014',
                'date' => now()->subDays(6),
                'location' => 'Laboratório Central',
                'status' => 'stored',
                'notified' => true,
            ],
            [
                'patient_id' => 5,
                'user_id' => 4, // Aluno 2
                'sample_type_id' => 4, // Urina
                'code' => 'SMP000015',
                'date' => now()->subDays(6),
                'location' => 'Laboratório Norte',
                'status' => 'stored',
                'notified' => true,
            ],
        ];

        foreach ($samples as $sample) {
            Sample::create($sample);
        }
    }
}
