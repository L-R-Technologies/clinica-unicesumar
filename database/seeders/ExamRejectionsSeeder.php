<?php

namespace Database\Seeders;

use App\Models\ExamRejection;
use Illuminate\Database\Seeder;

class ExamRejectionsSeeder extends Seeder
{
    public function run(): void
    {
        $examRejections = [
            [
                'exam_id' => 5, // Exame de Colesterol rejeitado
                'user_id' => 2, // Professor que rejeitou
                'justification' => 'Paciente não manteve jejum de 12 horas conforme protocolo. O consumo de álcool nas últimas 24h também pode ter interferido nos resultados lipídicos. Solicitada nova coleta após jejum adequado.',
            ],
        ];

        foreach ($examRejections as $rejection) {
            ExamRejection::create($rejection);
        }
    }
}
