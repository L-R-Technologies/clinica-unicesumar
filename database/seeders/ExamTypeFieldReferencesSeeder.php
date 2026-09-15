<?php

namespace Database\Seeders;

use App\Models\ExamTypeField;
use Illuminate\Database\Seeder;

/**
 * Valores de referência (faixa esperada para pessoa saudável) dos campos
 * numéricos criados em ExamTypeFieldsSeeder. Faixas sem sexo/idade valem
 * para todos; as demais têm prioridade quando o paciente se encaixa.
 */
class ExamTypeFieldReferencesSeeder extends Seeder
{
    private const HEMOGRAM = 1;

    private const GLUCOSE = 2;

    private const HBA1C = 3;

    private const CREATININE = 4;

    private const ALT = 5;

    private const AST = 6;

    private const ALKALINE_PHOSPHATASE = 7;

    private const GAMMA_GT = 8;

    private const ALBUMIN = 9;

    private const TOTAL_CHOLESTEROL = 10;

    private const TRIGLYCERIDES = 11;

    private const URINALYSIS = 16;

    public function run(): void
    {
        foreach ($this->references() as [$examTypeId, $fieldName, $references]) {
            $field = ExamTypeField::where('exam_type_id', $examTypeId)
                ->where('name', $fieldName)
                ->first();

            if (! $field) {
                continue;
            }

            foreach ($references as $reference) {
                $field->references()->create($reference);
            }
        }
    }

    /**
     * @return array<int, array{0: int, 1: string, 2: array<int, array<string, mixed>>}>
     */
    private function references(): array
    {
        return [
            // Hemograma Completo
            [self::HEMOGRAM, 'red_blood_cells', [
                ['sex' => 'male', 'age_min' => 18, 'min_value' => 4.5, 'max_value' => 6.0],
                ['sex' => 'female', 'age_min' => 18, 'min_value' => 4.0, 'max_value' => 5.4],
                ['age_max' => 17, 'min_value' => 4.0, 'max_value' => 5.5],
            ]],
            [self::HEMOGRAM, 'hemoglobin', [
                ['sex' => 'male', 'age_min' => 18, 'min_value' => 13.5, 'max_value' => 17.5],
                ['sex' => 'female', 'age_min' => 18, 'min_value' => 12.0, 'max_value' => 16.0],
                ['age_max' => 17, 'min_value' => 11.5, 'max_value' => 15.5],
            ]],
            [self::HEMOGRAM, 'hematocrit', [
                ['sex' => 'male', 'age_min' => 18, 'min_value' => 41, 'max_value' => 53],
                ['sex' => 'female', 'age_min' => 18, 'min_value' => 36, 'max_value' => 46],
                ['age_max' => 17, 'min_value' => 34, 'max_value' => 45],
            ]],
            [self::HEMOGRAM, 'mcv', [['min_value' => 80, 'max_value' => 100]]],
            [self::HEMOGRAM, 'mch', [['min_value' => 27, 'max_value' => 33]]],
            [self::HEMOGRAM, 'mchc', [['min_value' => 32, 'max_value' => 36]]],
            [self::HEMOGRAM, 'rdw', [['min_value' => 11.5, 'max_value' => 14.5]]],
            [self::HEMOGRAM, 'white_blood_cells', [
                ['age_min' => 18, 'min_value' => 4000, 'max_value' => 11000],
                ['age_max' => 17, 'min_value' => 5000, 'max_value' => 13000],
            ]],
            [self::HEMOGRAM, 'neutrophils_percent', [['min_value' => 40, 'max_value' => 70]]],
            [self::HEMOGRAM, 'lymphocytes_percent', [['min_value' => 20, 'max_value' => 45]]],
            [self::HEMOGRAM, 'monocytes_percent', [['min_value' => 2, 'max_value' => 10]]],
            [self::HEMOGRAM, 'eosinophils_percent', [['min_value' => 1, 'max_value' => 5]]],
            [self::HEMOGRAM, 'basophils_percent', [['min_value' => 0, 'max_value' => 1]]],
            [self::HEMOGRAM, 'platelets', [['min_value' => 150000, 'max_value' => 450000]]],

            // Bioquímica
            [self::GLUCOSE, 'glucose', [['min_value' => 70, 'max_value' => 99]]],
            [self::HBA1C, 'hba1c', [['min_value' => 4.0, 'max_value' => 5.6]]],
            [self::CREATININE, 'creatinine', [
                ['sex' => 'male', 'age_min' => 18, 'min_value' => 0.7, 'max_value' => 1.3],
                ['sex' => 'female', 'age_min' => 18, 'min_value' => 0.6, 'max_value' => 1.1],
                ['age_max' => 17, 'min_value' => 0.3, 'max_value' => 0.9],
            ]],
            [self::ALT, 'alt', [
                ['sex' => 'male', 'max_value' => 41],
                ['sex' => 'female', 'max_value' => 33],
            ]],
            [self::AST, 'ast', [
                ['sex' => 'male', 'max_value' => 40],
                ['sex' => 'female', 'max_value' => 32],
            ]],
            [self::ALKALINE_PHOSPHATASE, 'alkaline_phosphatase', [
                ['age_min' => 18, 'min_value' => 40, 'max_value' => 130],
                ['age_max' => 17, 'min_value' => 100, 'max_value' => 390],
            ]],
            [self::GAMMA_GT, 'gamma_gt', [
                ['sex' => 'male', 'min_value' => 8, 'max_value' => 61],
                ['sex' => 'female', 'min_value' => 5, 'max_value' => 36],
            ]],
            [self::ALBUMIN, 'albumin', [['min_value' => 3.5, 'max_value' => 5.2]]],
            [self::TOTAL_CHOLESTEROL, 'total_cholesterol', [
                ['age_min' => 20, 'max_value' => 190],
                ['age_max' => 19, 'max_value' => 170],
            ]],
            [self::TRIGLYCERIDES, 'triglycerides', [
                ['age_min' => 20, 'max_value' => 150],
                ['age_min' => 10, 'age_max' => 19, 'max_value' => 90],
                ['age_max' => 9, 'max_value' => 75],
            ]],

            // Urina tipo I
            [self::URINALYSIS, 'density', [['min_value' => 1.005, 'max_value' => 1.030]]],
            [self::URINALYSIS, 'ph', [['min_value' => 5.0, 'max_value' => 8.0]]],
        ];
    }
}
