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
        $exams = [
            // Exames da Paciente 1 (Maria Silva Santos)
            [
                'user_id' => 2, // Professor
                'patient_history_id' => 1,
                'patient_id' => 1,
                'exam_type_id' => 1, // Hemograma Completo
                'sample_id' => 1,
                'date' => now()->subDays(5),
                'results' => [
                    'red_blood_cells' => 4.5,
                    'hemoglobin' => 14.2,
                    'hematocrit' => 42.5,
                    'mcv' => 88.0,
                    'mch' => 31.5,
                    'mchc' => 33.6,
                    'rdw' => 12.5,
                    'white_blood_cells' => 7200,
                    'neutrophils_percent' => 60.0,
                    'lymphocytes_percent' => 30.0,
                    'monocytes_percent' => 7.0,
                    'eosinophils_percent' => 2.5,
                    'basophils_percent' => 0.5,
                    'platelets' => 250000,
                ],
                'status' => 'approved',
                'observation' => 'Hemograma dentro dos parâmetros normais.',
            ],
            [
                'user_id' => 3, // Aluno 1
                'patient_history_id' => 1,
                'patient_id' => 1,
                'exam_type_id' => 2, // Glicemia
                'sample_id' => 2,
                'date' => now()->subDays(5),
                'results' => [
                    'glucose' => 88.0,
                ],
                'status' => 'approved',
                'observation' => 'Glicemia de jejum normal.',
            ],
            [
                'user_id' => 4, // Aluno 2
                'patient_history_id' => 2,
                'patient_id' => 1,
                'exam_type_id' => 16, // Urina Tipo I
                'sample_id' => 3,
                'date' => now()->subDays(2),
                'results' => [
                    'volume' => 50.0,
                    'color' => 'Amarelo claro',
                    'aspect' => 'Límpido',
                    'density' => 1.020,
                    'ph' => 6.0,
                    'protein' => 'Negativo',
                    'glucose' => 'Negativo',
                    'red_blood_cells' => 'Raros',
                    'white_blood_cells' => 'Raros',
                ],
                'status' => 'approved',
                'observation' => 'Urina tipo I sem alterações.',
            ],

            // Exames do Paciente 2 (João Carlos Oliveira)
            [
                'user_id' => 2, // Professor
                'patient_history_id' => 3,
                'patient_id' => 2,
                'exam_type_id' => 1, // Hemograma Completo
                'sample_id' => 4,
                'date' => now()->subDays(1),
                'results' => [
                    'red_blood_cells' => 4.2,
                    'hemoglobin' => 13.5,
                    'hematocrit' => 40.0,
                    'mcv' => 90.0,
                    'mch' => 32.1,
                    'mchc' => 33.7,
                    'rdw' => 13.2,
                    'white_blood_cells' => 8500,
                    'neutrophils_percent' => 65.0,
                    'lymphocytes_percent' => 25.0,
                    'monocytes_percent' => 8.0,
                    'eosinophils_percent' => 1.5,
                    'basophils_percent' => 0.5,
                    'platelets' => 320000,
                ],
                'status' => 'pending_approval',
                'observation' => 'Leucocitose discreta. Avaliar processo inflamatório.',
            ],
            [
                'user_id' => 3, // Aluno 1
                'patient_history_id' => 3,
                'patient_id' => 2,
                'exam_type_id' => 10, // Colesterol Total
                'sample_id' => 5,
                'date' => now()->subDays(1),
                'results' => [
                    'total_cholesterol' => 220.0,
                ],
                'status' => 'rejected',
                'observation' => 'Paciente não manteve jejum adequado conforme histórico.',
            ],
            [
                'user_id' => 4, // Aluno 2
                'patient_history_id' => 3,
                'patient_id' => 2,
                'exam_type_id' => 19, // Parasitológico de Fezes
                'sample_id' => 6,
                'date' => now()->subDays(1),
                'results' => [
                    'consistency' => 'Pastosa',
                    'color' => 'Marrom',
                    'helminth_eggs' => 'Ausentes',
                    'protozoa' => 'Ausentes',
                    'final_result' => 'Ausência de parasitas e comensais',
                ],
                'status' => 'pending',
                'observation' => 'Parasitológico de fezes para investigação de sintomas gastrointestinais.',
            ],
            [
                'user_id' => 2, // Professor
                'patient_history_id' => 3,
                'patient_id' => 2,
                'exam_type_id' => 16, // Urina Tipo I
                'sample_id' => 7,
                'date' => now()->subDays(1),
                'results' => [
                    'volume' => 40.0,
                    'color' => 'Amarelo escuro',
                    'aspect' => 'Turvo',
                    'density' => 1.025,
                    'ph' => 5.5,
                    'protein' => 'Traços',
                    'glucose' => 'Negativo',
                    'red_blood_cells' => 'Numerosos',
                    'white_blood_cells' => 'Numerosos',
                ],
                'status' => 'approved',
                'observation' => 'Urina concentrada. Recomendar aumento da ingesta hídrica.',
            ],
        ];

        foreach ($exams as $exam) {
            Exam::create($exam);
        }
    }
}
