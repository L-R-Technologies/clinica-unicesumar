<?php

namespace Database\Seeders;

use App\Models\ExamTypeField;
use Illuminate\Database\Seeder;

class ExamTypeFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $examTypeFields = [
            // Hemograma Completo (ID: 1)
            [
                'exam_type_id' => 1,
                'name' => 'red_blood_cells',
                'label' => 'Eritrócitos',
                'field_type' => 'float',
                'unit' => 'milhões/mm³',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'hemoglobin',
                'label' => 'Hemoglobina',
                'field_type' => 'float',
                'unit' => 'g/dL',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'hematocrit',
                'label' => 'Hematócrito',
                'field_type' => 'float',
                'unit' => '%',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'mcv',
                'label' => 'VCM',
                'field_type' => 'float',
                'unit' => 'fL',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'mch',
                'label' => 'HCM',
                'field_type' => 'float',
                'unit' => 'pg',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'mchc',
                'label' => 'CHCM',
                'field_type' => 'float',
                'unit' => 'g/dL',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'rdw',
                'label' => 'RDW',
                'field_type' => 'float',
                'unit' => '%',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'white_blood_cells',
                'label' => 'Leucócitos',
                'field_type' => 'int',
                'unit' => '/µL',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'neutrophils_percent',
                'label' => 'Neutrófilos',
                'field_type' => 'float',
                'unit' => '%',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'lymphocytes_percent',
                'label' => 'Linfócitos',
                'field_type' => 'float',
                'unit' => '%',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'monocytes_percent',
                'label' => 'Monócitos',
                'field_type' => 'float',
                'unit' => '%',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'eosinophils_percent',
                'label' => 'Eosinófilos',
                'field_type' => 'float',
                'unit' => '%',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'basophils_percent',
                'label' => 'Basófilos',
                'field_type' => 'float',
                'unit' => '%',
            ],
            [
                'exam_type_id' => 1,
                'name' => 'platelets',
                'label' => 'Plaquetas',
                'field_type' => 'int',
                'unit' => '/mm³',
            ],

            // Glicemia (ID: 2)
            [
                'exam_type_id' => 2,
                'name' => 'glucose',
                'label' => 'Glicose',
                'field_type' => 'float',
                'unit' => 'mg/dL',
            ],

            // Hemoglobina Glicada (ID: 3)
            [
                'exam_type_id' => 3,
                'name' => 'hba1c',
                'label' => 'Hemoglobina Glicada',
                'field_type' => 'float',
                'unit' => '%',
            ],

            // Creatinina (ID: 4)
            [
                'exam_type_id' => 4,
                'name' => 'creatinine',
                'label' => 'Creatinina',
                'field_type' => 'float',
                'unit' => 'mg/dL',
            ],

            // ALT/TGP (ID: 5)
            [
                'exam_type_id' => 5,
                'name' => 'alt',
                'label' => 'ALT/TGP',
                'field_type' => 'float',
                'unit' => 'U/L',
            ],

            // AST/TGO (ID: 6)
            [
                'exam_type_id' => 6,
                'name' => 'ast',
                'label' => 'AST/TGO',
                'field_type' => 'float',
                'unit' => 'U/L',
            ],

            // Fosfatase Alcalina (ID: 7)
            [
                'exam_type_id' => 7,
                'name' => 'alkaline_phosphatase',
                'label' => 'Fosfatase Alcalina',
                'field_type' => 'float',
                'unit' => 'U/L',
            ],

            // Gama GT (ID: 8)
            [
                'exam_type_id' => 8,
                'name' => 'gamma_gt',
                'label' => 'Gama GT',
                'field_type' => 'float',
                'unit' => 'U/L',
            ],

            // Albumina (ID: 9)
            [
                'exam_type_id' => 9,
                'name' => 'albumin',
                'label' => 'Albumina',
                'field_type' => 'float',
                'unit' => 'g/dL',
            ],

            // Colesterol Total (ID: 10)
            [
                'exam_type_id' => 10,
                'name' => 'total_cholesterol',
                'label' => 'Colesterol Total',
                'field_type' => 'float',
                'unit' => 'mg/dL',
            ],

            // Triglicerídeos (ID: 11)
            [
                'exam_type_id' => 11,
                'name' => 'triglycerides',
                'label' => 'Triglicerídeos',
                'field_type' => 'float',
                'unit' => 'mg/dL',
            ],

            // Beta HCG (ID: 12)
            [
                'exam_type_id' => 12,
                'name' => 'hcg',
                'label' => 'Beta HCG',
                'field_type' => 'string',
                'unit' => null,
            ],

            // Tipagem Sanguínea (ID: 13)
            [
                'exam_type_id' => 13,
                'name' => 'abo_group',
                'label' => 'Grupo ABO',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 13,
                'name' => 'rh_factor',
                'label' => 'Fator Rh',
                'field_type' => 'string',
                'unit' => null,
            ],

            // VDRL (ID: 14)
            [
                'exam_type_id' => 14,
                'name' => 'vdrl',
                'label' => 'VDRL',
                'field_type' => 'string',
                'unit' => null,
            ],

            // Teste Rápido de Sífilis (ID: 15)
            [
                'exam_type_id' => 15,
                'name' => 'rapid_test_syphilis',
                'label' => 'Teste Rápido de Sífilis',
                'field_type' => 'string',
                'unit' => null,
            ],

            // Urina Tipo I (ID: 16)
            [
                'exam_type_id' => 16,
                'name' => 'volume',
                'label' => 'Volume',
                'field_type' => 'float',
                'unit' => 'mL',
            ],
            [
                'exam_type_id' => 16,
                'name' => 'color',
                'label' => 'Cor',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 16,
                'name' => 'aspect',
                'label' => 'Aspecto',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 16,
                'name' => 'density',
                'label' => 'Densidade',
                'field_type' => 'float',
                'unit' => null,
            ],
            [
                'exam_type_id' => 16,
                'name' => 'ph',
                'label' => 'pH',
                'field_type' => 'float',
                'unit' => null,
            ],
            [
                'exam_type_id' => 16,
                'name' => 'protein',
                'label' => 'Proteína',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 16,
                'name' => 'glucose',
                'label' => 'Glicose',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 16,
                'name' => 'red_blood_cells',
                'label' => 'Eritrócitos',
                'field_type' => 'string',
                'unit' => '/mL',
            ],
            [
                'exam_type_id' => 16,
                'name' => 'white_blood_cells',
                'label' => 'Leucócitos',
                'field_type' => 'string',
                'unit' => '/mL',
            ],

            // Bacterioscopia (Gram) (ID: 17)
            [
                'exam_type_id' => 17,
                'name' => 'gram_stain_result',
                'label' => 'Resultado da Bacterioscopia',
                'field_type' => 'string',
                'unit' => null,
            ],

            // Crescimento Cromogênico (ID: 18)
            [
                'exam_type_id' => 18,
                'name' => 'growth_result',
                'label' => 'Resultado do Crescimento',
                'field_type' => 'string',
                'unit' => null,
            ],

            // Parasitológico de Fezes (ID: 19)
            [
                'exam_type_id' => 19,
                'name' => 'consistency',
                'label' => 'Consistência',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 19,
                'name' => 'color',
                'label' => 'Cor',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 19,
                'name' => 'helminth_eggs',
                'label' => 'Ovos de Helmintos',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 19,
                'name' => 'protozoa',
                'label' => 'Protozoários',
                'field_type' => 'string',
                'unit' => null,
            ],
            [
                'exam_type_id' => 19,
                'name' => 'final_result',
                'label' => 'Resultado Final',
                'field_type' => 'string',
                'unit' => null,
            ],
        ];

        foreach ($examTypeFields as $field) {
            ExamTypeField::create($field);
        }
    }
}
