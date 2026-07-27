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
                'clarity' => 5, // Escala de 1-5
                'cordiality' => 5,
                'waiting_time' => 4,
                'result_speed' => 5,
                'confidence' => 5,
                'observation' => 'Excelente atendimento, profissionais muito preparados. Explicações claras sobre o procedimento.',
            ],
            [
                'exam_id' => 2, // Glicemia da Paciente 1
                'clarity' => 4,
                'cordiality' => 5,
                'waiting_time' => 3,
                'result_speed' => 4,
                'confidence' => 4,
                'observation' => 'Bom atendimento, apenas o tempo de espera foi um pouco longo.',
            ],
            [
                'exam_id' => 3, // Urina da Paciente 1
                'clarity' => 5,
                'cordiality' => 5,
                'waiting_time' => 5,
                'result_speed' => 5,
                'confidence' => 5,
                'observation' => 'Perfeito! Muito satisfeito com todo o processo. Atendimento rápido e eficiente.',
            ],
            [
                'exam_id' => 7, // Urina do Paciente 2
                'clarity' => 3,
                'cordiality' => 4,
                'waiting_time' => 2,
                'result_speed' => 3,
                'confidence' => 3,
                'observation' => 'Atendimento satisfatório, mas o laboratório estava muito cheio e demorou para ser atendido.',
            ],
        ];

        foreach ($examFeedbacks as $feedback) {
            ExamFeedback::create($feedback);
        }
    }
}
