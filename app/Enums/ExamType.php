<?php

namespace App\Enums;

enum ExamType: string
{
    case COMPLETE_BLOOD_COUNT = 'complete_blood_count'; // Hemograma Completo
    case GLUCOSE = 'glucose'; // Glicemia
    case GLYCATED_HEMOGLOBIN = 'glycated_hemoglobin'; // Hemoglobina Glicada
    case CREATININE = 'creatinine'; // Creatinina
    case ALT_SGPT = 'alt_sgpt'; // ALT/TGP
    case AST_SGOT = 'ast_sgot'; // AST/TGO
    case ALKALINE_PHOSPHATASE = 'alkaline_phosphatase'; // Fosfatase Alcalina
    case GAMMA_GT = 'gamma_gt'; // Gama GT
    case ALBUMIN = 'albumin'; // Albumina
    case TOTAL_CHOLESTEROL = 'total_cholesterol'; // Colesterol Total
    case TRIGLYCERIDES = 'triglycerides'; // Triglicerídeos
    case HCG = 'hcg'; // Beta HCG
    case BLOOD_TYPING = 'blood_typing'; // Tipagem Sanguínea
    case VDRL = 'vdrl'; // VDRL
    case SYPHILIS_RAPID_TEST = 'syphilis_rapid_test'; // Teste Rápido de Sífilis
    case URINALYSIS = 'urinalysis'; // Urina Tipo I
    case GRAM_STAIN = 'gram_stain'; // Bacterioscopia (Gram)
    case CHROMOGENIC_GROWTH = 'chromogenic_growth'; // Crescimento Cromogênico
    case STOOL_PARASITOLOGY = 'stool_parasitology'; // Parasitológico de Fezes
    case GENERIC_TEST = 'generic_test'; // Exame Genérico

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPLETE_BLOOD_COUNT => 'Hemograma Completo',
            self::GLUCOSE => 'Glicemia',
            self::GLYCATED_HEMOGLOBIN => 'Hemoglobina Glicada',
            self::CREATININE => 'Creatinina',
            self::ALT_SGPT => 'ALT/TGP',
            self::AST_SGOT => 'AST/TGO',
            self::ALKALINE_PHOSPHATASE => 'Fosfatase Alcalina',
            self::GAMMA_GT => 'Gama GT',
            self::ALBUMIN => 'Albumina',
            self::TOTAL_CHOLESTEROL => 'Colesterol Total',
            self::TRIGLYCERIDES => 'Triglicerídeos',
            self::HCG => 'Beta HCG',
            self::BLOOD_TYPING => 'Tipagem Sanguínea',
            self::VDRL => 'VDRL',
            self::SYPHILIS_RAPID_TEST => 'Teste Rápido de Sífilis',
            self::URINALYSIS => 'Urina Tipo I',
            self::GRAM_STAIN => 'Bacterioscopia (Gram)',
            self::CHROMOGENIC_GROWTH => 'Crescimento Cromogênico',
            self::STOOL_PARASITOLOGY => 'Parasitológico de Fezes',
            self::GENERIC_TEST => 'Exame Genérico',
        };
    }

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }

    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->getLabel() === $label) {
                return $case;
            }
        }

        return null;
    }

    public function getDefaultResults(): array
    {
        return match ($this) {
            self::COMPLETE_BLOOD_COUNT => [
                'red_blood_cells' => '',
                'hemoglobin' => '',
                'hematocrit' => '',
                'mcv' => '',
                'mch' => '',
                'mchc' => '',
                'rdw' => '',
                'white_blood_cells' => '',
                'neutrophils_percent' => '',
                'neutrophils_absolute' => '',
                'eosinophils_percent' => '',
                'eosinophils_absolute' => '',
                'basophils_percent' => '',
                'basophils_absolute' => '',
                'monocytes_percent' => '',
                'monocytes_absolute' => '',
                'lymphocytes_percent' => '',
                'lymphocytes_absolute' => '',
                'platelets' => '',
            ],
            self::GLUCOSE => [
                'glucose' => '',
            ],
            self::GLYCATED_HEMOGLOBIN => [
                'hba1c' => '',
            ],
            self::CREATININE => [
                'creatinine' => '',
            ],
            self::ALT_SGPT => [
                'alt' => '',
            ],
            self::AST_SGOT => [
                'ast' => '',
            ],
            self::ALKALINE_PHOSPHATASE => [
                'alkaline_phosphatase' => '',
            ],
            self::ALBUMIN => [
                'albumin' => '',
            ],
            self::GAMMA_GT => [
                'gamma_gt' => '',
            ],
            self::TOTAL_CHOLESTEROL => [
                'total_cholesterol' => '',
            ],
            self::TRIGLYCERIDES => [
                'triglycerides' => '',
            ],
            self::HCG => [
                'hcg' => '',
            ],
            self::BLOOD_TYPING => [
                'abo_group' => '',
                'rh_factor' => '',
            ],
            self::VDRL => [
                'vdrl' => '',
            ],
            self::SYPHILIS_RAPID_TEST => [
                'rapid_test_syphilis' => '',
            ],
            self::URINALYSIS => [
                'volume' => '',
                'color' => '',
                'aspect' => '',
                'density' => '',
                'ph' => '',
                'nitrite' => '',
                'protein' => '',
                'ketones' => '',
                'glucose' => '',
                'bilirubin' => '',
                'urobilinogen' => '',
                'hemoglobin' => '',
                'leukocytes' => '',
                'epithelial_cells' => '',
                'red_blood_cells' => '',
                'white_blood_cells' => '',
                'mucus_filaments' => '',
                'casts' => '',
                'crystals' => '',
                'observations' => '',
            ],
            self::GRAM_STAIN => [
                'gram_stain_result' => '',
            ],
            self::CHROMOGENIC_GROWTH => [
                'growth_result' => '',
            ],
            self::STOOL_PARASITOLOGY => [
                'consistency' => '',
                'color' => '',
                'mucus' => '',
                'blood' => '',
                'helminth_eggs' => '',
                'protozoa' => '',
                'final_result' => '',
            ],
            self::GENERIC_TEST => [],
        };
    }

    public function getResultsLabels(): array
    {
        return match ($this) {
            self::COMPLETE_BLOOD_COUNT => [
                'red_blood_cells' => 'Eritrócitos (milhões/mm³)',
                'hemoglobin' => 'Hemoglobina (g/dL)',
                'hematocrit' => 'Hematócrito (%)',
                'mcv' => 'VCM (fL)',
                'mch' => 'HCM (pg)',
                'mchc' => 'CHCM (g/dL)',
                'rdw' => 'RDW (%)',
                'white_blood_cells' => 'Leucócitos (/µL)',
                'neutrophils_percent' => 'Neutrófilos (%)',
                'neutrophils_absolute' => 'Neutrófilos Absoluto (/µL)',
                'eosinophils_percent' => 'Eosinófilos (%)',
                'eosinophils_absolute' => 'Eosinófilos Absoluto (/µL)',
                'basophils_percent' => 'Basófilos (%)',
                'basophils_absolute' => 'Basófilos Absoluto (/µL)',
                'monocytes_percent' => 'Monócitos (%)',
                'monocytes_absolute' => 'Monócitos Absoluto (/µL)',
                'lymphocytes_percent' => 'Linfócitos (%)',
                'lymphocytes_absolute' => 'Linfócitos Absoluto (/µL)',
                'platelets' => 'Plaquetas (/mm³)',
            ],
            self::GLUCOSE => [
                'glucose' => 'Glicose (mg/dL)',
            ],
            self::GLYCATED_HEMOGLOBIN => [
                'hba1c' => 'Hemoglobina Glicada (%)',
            ],
            self::CREATININE => [
                'creatinine' => 'Creatinina (mg/dL)',
            ],
            self::ALT_SGPT => [
                'alt' => 'ALT/TGP (U/L)',
            ],
            self::AST_SGOT => [
                'ast' => 'AST/TGO (U/L)',
            ],
            self::ALKALINE_PHOSPHATASE => [
                'alkaline_phosphatase' => 'Fosfatase Alcalina (U/L)',
            ],
            self::ALBUMIN => [
                'albumin' => 'Albumina (g/dL)',
            ],
            self::GAMMA_GT => [
                'gamma_gt' => 'Gama GT (U/L)',
            ],
            self::TOTAL_CHOLESTEROL => [
                'total_cholesterol' => 'Colesterol Total (mg/dL)',
            ],
            self::TRIGLYCERIDES => [
                'triglycerides' => 'Triglicerídeos (mg/dL)',
            ],
            self::HCG => [
                'hcg' => 'Beta HCG',
            ],
            self::BLOOD_TYPING => [
                'abo_group' => 'Grupo ABO',
                'rh_factor' => 'Fator Rh',
            ],
            self::VDRL => [
                'vdrl' => 'VDRL',
            ],
            self::SYPHILIS_RAPID_TEST => [
                'rapid_test_syphilis' => 'Teste Rápido de Sífilis',
            ],
            self::URINALYSIS => [
                'volume' => 'Volume (mL)',
                'color' => 'Cor',
                'aspect' => 'Aspecto',
                'density' => 'Densidade',
                'ph' => 'pH',
                'nitrite' => 'Nitrito',
                'protein' => 'Proteína',
                'ketones' => 'Cetonas',
                'glucose' => 'Glicose',
                'bilirubin' => 'Bilirrubina',
                'urobilinogen' => 'Urobilinogênio',
                'hemoglobin' => 'Hemoglobina',
                'leukocytes' => 'Leucócitos',
                'epithelial_cells' => 'Células Epiteliais (/mL)',
                'red_blood_cells' => 'Eritrócitos (/mL)',
                'white_blood_cells' => 'Leucócitos (/mL)',
                'mucus_filaments' => 'Filamentos de Muco',
                'casts' => 'Cilindros',
                'crystals' => 'Cristais',
                'observations' => 'Observações',
            ],
            self::GRAM_STAIN => [
                'gram_stain_result' => 'Resultado da Bacterioscopia',
            ],
            self::CHROMOGENIC_GROWTH => [
                'growth_result' => 'Resultado do Crescimento',
            ],
            self::STOOL_PARASITOLOGY => [
                'consistency' => 'Consistência',
                'color' => 'Cor',
                'mucus' => 'Muco',
                'blood' => 'Sangue',
                'helminth_eggs' => 'Ovos de Helmintos',
                'protozoa' => 'Protozoários',
                'final_result' => 'Resultado Final',
            ],
            self::GENERIC_TEST => [],
        };
    }
}
