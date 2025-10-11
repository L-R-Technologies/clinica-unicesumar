<?php

namespace Database\Seeders;

use App\Models\ExamFeedback;
use Illuminate\Database\Seeder;

class ExamFeedbacksSeeder extends Seeder
{
    public function run(): void
    {
        $examFeedbacks = [
            [
                'exam_id' => 1, // Hemograma da Paciente 1
                'clarity' => 9, // Escala de 1-10
                'cordiality' => 10,
                'waiting_time' => 8,
                'result_speed' => 9,
                'confidence' => 9,
                'observation' => 'Excelente atendimento, profissionais muito preparados. Explicações claras sobre o procedimento.',
            ],
            [
                'exam_id' => 2, // Glicemia da Paciente 1
                'clarity' => 8,
                'cordiality' => 9,
                'waiting_time' => 7,
                'result_speed' => 8,
                'confidence' => 8,
                'observation' => 'Bom atendimento, apenas o tempo de espera foi um pouco longo.',
            ],
            [
                'exam_id' => 3, // Urina da Paciente 1
                'clarity' => 10,
                'cordiality' => 10,
                'waiting_time' => 9,
                'result_speed' => 10,
                'confidence' => 10,
                'observation' => 'Perfeito! Muito satisfeito com todo o processo. Atendimento rápido e eficiente.',
            ],
            [
                'exam_id' => 7, // Urina do Paciente 2
                'clarity' => 7,
                'cordiality' => 8,
                'waiting_time' => 6,
                'result_speed' => 7,
                'confidence' => 7,
                'observation' => 'Atendimento satisfatório, mas o laboratório estava muito cheio e demorou para ser atendido.',
            ],
        ];

        foreach ($examFeedbacks as $feedback) {
            ExamFeedback::create($feedback);
        }
    }
}
