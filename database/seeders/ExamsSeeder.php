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
                'patient_id' => 2,
                'patient_history_id' => 3,
                'exam_type_id' => 5, // Urinálise Completa
                'sample_id' => 7,
                'date' => now()->subDays(1),
                'results' => [
                    'densidade' => '1.022',
                    'ph' => '6.0',
                    'glicose' => 'Negativo',
                    'proteinas' => 'Traços',
                    'cetonas' => 'Negativo',
                    'sedimento' => 'Leucócitos: 2-4/campo',
                    'cristais' => 'Ausentes',
                    'observacoes' => 'Ligeira proteinúria',
                ],
                'status' => 'approved',
                'observation' => 'Ligeira proteinúria',
            ],

            // Exames da Paciente 3 (Ana Paula Costa) - Investigação de Anemia
            [
                'user_id' => 3, // Aluno 1
                'patient_id' => 3,
                'patient_history_id' => 4,
                'exam_type_id' => 1, // Hemograma Completo
                'sample_id' => 16, // Nova amostra específica para este exame
                'date' => now()->subDays(3),
                'results' => [
                    'hemoglobina' => '9.8',
                    'hematocrito' => '29.5',
                    'hemaceas' => '4.2',
                    'vcm' => '70.2',
                    'hcm' => '23.3',
                    'chcm' => '33.2',
                    'leucocitos' => '5800',
                    'plaquetas' => '280000',
                    'observacoes' => 'Anemia microcítica hipocrômica',
                ],
                'status' => 'approved',
                'observation' => 'Anemia microcítica hipocrômica',
            ],
            [
                'user_id' => 4, // Aluno 2
                'patient_id' => 3,
                'patient_history_id' => 4,
                'exam_type_id' => 3, // Ferro Sérico
                'sample_id' => 8,
                'date' => now()->subDays(3),
                'results' => [
                    'ferro_serico' => '45',
                    'capacidade_ligacao_ferro' => '420',
                    'transferrina' => '380',
                    'saturacao_transferrina' => '10.7',
                    'observacoes' => 'Deficiência de ferro',
                ],
                'status' => 'approved',
                'observation' => 'Deficiência de ferro',
            ],

            // Exames do Paciente 4 (Carlos Eduardo Ferreira) - Controle Diabetes
            [
                'user_id' => 2, // Professor
                'patient_id' => 4,
                'patient_history_id' => 5,
                'exam_type_id' => 2, // Glicose em Jejum
                'sample_id' => 9,
                'date' => now()->subDays(4),
                'results' => [
                    'glicose_jejum' => '145',
                    'observacoes' => 'Hiperglicemia leve',
                ],
                'status' => 'approved',
                'observation' => 'Hiperglicemia leve',
            ],
            [
                'user_id' => 3, // Aluno 1
                'patient_id' => 4,
                'patient_history_id' => 5,
                'exam_type_id' => 8, // Hemoglobina Glicada
                'sample_id' => 10,
                'date' => now()->subDays(4),
                'results' => [
                    'hba1c' => '7.8',
                    'observacoes' => 'Controle glicêmico inadequado',
                ],
                'status' => 'approved',
                'observation' => 'Controle glicêmico inadequado',
            ],
            [
                'user_id' => 4, // Aluno 2
                'patient_id' => 4,
                'patient_history_id' => 5,
                'exam_type_id' => 5, // Urinálise Completa
                'sample_id' => 17, // Nova amostra específica para este exame
                'date' => now()->subDays(4),
                'results' => [
                    'densidade' => '1.030',
                    'ph' => '5.5',
                    'glicose' => 'Positivo (+)',
                    'proteinas' => 'Traços',
                    'cetonas' => 'Negativo',
                    'sedimento' => 'Normal',
                    'cristais' => 'Ausentes',
                    'observacoes' => 'Glicosúria presente',
                ],
                'status' => 'approved',
                'observation' => 'Glicosúria presente',
            ],

            // Exames da Paciente 5 (Fernanda Lima Silva) - Controle Hipotireoidismo
            [
                'user_id' => 2, // Professor
                'patient_id' => 5,
                'patient_history_id' => 6,
                'exam_type_id' => 7, // TSH
                'sample_id' => 11,
                'date' => now()->subDays(6),
                'results' => [
                    'tsh' => '8.5',
                    'observacoes' => 'TSH elevado - hipotireoidismo',
                ],
                'status' => 'approved',
                'observation' => 'TSH elevado - hipotireoidismo',
            ],
            [
                'user_id' => 3, // Aluno 1
                'patient_id' => 5,
                'patient_history_id' => 6,
                'exam_type_id' => 6, // T4 Livre
                'sample_id' => 12,
                'date' => now()->subDays(6),
                'results' => [
                    't4_livre' => '0.8',
                    'observacoes' => 'T4 livre no limite inferior',
                ],
                'status' => 'approved',
                'observation' => 'T4 livre no limite inferior',
            ],
            [
                'user_id' => 4, // Aluno 2
                'patient_id' => 5,
                'patient_history_id' => 6,
                'exam_type_id' => 1, // Hemograma Completo
                'sample_id' => 13,
                'date' => now()->subDays(6),
                'results' => [
                    'hemoglobina' => '12.5',
                    'hematocrito' => '37.2',
                    'hemaceas' => '4.1',
                    'vcm' => '90.7',
                    'hcm' => '30.5',
                    'chcm' => '33.6',
                    'leucocitos' => '6200',
                    'plaquetas' => '320000',
                    'observacoes' => 'Hemograma dentro da normalidade',
                ],
                'status' => 'approved',
                'observation' => 'Hemograma dentro da normalidade',
            ],
        ];

        foreach ($exams as $exam) {
            Exam::create($exam);
        }
    }
}
