<?php

namespace Database\Seeders;

use App\Models\ExamType;
use Illuminate\Database\Seeder;

class ExamTypesSeeder extends Seeder
{
    public function run(): void
    {
        $examTypes = [
            [
                'name' => 'Hemograma Completo',
                'description' => 'Exame que avalia as células do sangue (hemácias, leucócitos e plaquetas) e seus índices hematimétricos.',
            ],
            [
                'name' => 'Glicemia',
                'description' => 'Dosagem da glicose no sangue, importante para diagnóstico e controle do diabetes.',
            ],
            [
                'name' => 'Hemoglobina Glicada',
                'description' => 'Exame que indica a média da glicemia nos últimos 2-3 meses.',
            ],
            [
                'name' => 'Creatinina',
                'description' => 'Exame que avalia a função renal através da dosagem de creatinina sérica.',
            ],
            [
                'name' => 'ALT/TGP',
                'description' => 'Enzima hepática que indica lesão das células do fígado.',
            ],
            [
                'name' => 'AST/TGO',
                'description' => 'Enzima presente no fígado, coração e músculos, indica lesão celular.',
            ],
            [
                'name' => 'Fosfatase Alcalina',
                'description' => 'Enzima presente no fígado e ossos, útil para avaliar função hepática e óssea.',
            ],
            [
                'name' => 'Gama GT',
                'description' => 'Enzima hepática sensível ao consumo de álcool e medicamentos.',
            ],
            [
                'name' => 'Albumina',
                'description' => 'Proteína produzida pelo fígado, indica estado nutricional e função hepática.',
            ],
            [
                'name' => 'Colesterol Total',
                'description' => 'Dosagem do colesterol total no sangue para avaliação cardiovascular.',
            ],
            [
                'name' => 'Triglicerídeos',
                'description' => 'Tipo de gordura no sangue, importante para avaliação cardiovascular.',
            ],
            [
                'name' => 'Beta HCG',
                'description' => 'Hormônio da gravidez, usado para diagnóstico de gestação.',
            ],
            [
                'name' => 'Tipagem Sanguínea',
                'description' => 'Determinação do grupo sanguíneo ABO e fator Rh.',
            ],
            [
                'name' => 'VDRL',
                'description' => 'Teste sorológico para triagem de sífilis.',
            ],
            [
                'name' => 'Teste Rápido de Sífilis',
                'description' => 'Teste rápido para detecção de anticorpos contra Treponema pallidum.',
            ],
            [
                'name' => 'Urina Tipo I',
                'description' => 'Exame básico de urina que avalia características físicas, químicas e microscópicas.',
            ],
            [
                'name' => 'Bacterioscopia (Gram)',
                'description' => 'Exame microscópico para identificação de bactérias através da coloração de Gram.',
            ],
            [
                'name' => 'Crescimento Cromogênico',
                'description' => 'Método de identificação de microrganismos através de meios cromogênicos.',
            ],
            [
                'name' => 'Parasitológico de Fezes',
                'description' => 'Exame para detecção de parasitas intestinais nas fezes.',
            ],
        ];

        foreach ($examTypes as $examType) {
            ExamType::create($examType);
        }
    }
}
