<?php

namespace App\Enums;

enum SampleType: string
{
    case WHOLE_BLOOD = 'whole_blood'; // Sangue Total
    case SERUM = 'serum'; // Soro
    case PLASMA = 'plasma'; // Plasma
    case URINE = 'urine'; // Urina
    case STOOL = 'stool'; // Fezes
    case VAGINAL_SWAB = 'vaginal_swab'; // Swab Vaginal
    case URETHRAL_SWAB = 'urethral_swab'; // Swab Uretral
    case THROAT_SWAB = 'throat_swab'; // Swab de Orofaringe
    case SPUTUM = 'sputum'; // Escarro
    case CSF = 'csf'; // Líquor (LCR)
    case SECRETION = 'secretion'; // Secreção

    public function getLabel(): string
    {
        return match ($this) {
            self::WHOLE_BLOOD => 'Sangue Total',
            self::SERUM => 'Soro',
            self::PLASMA => 'Plasma',
            self::URINE => 'Urina',
            self::STOOL => 'Fezes',
            self::VAGINAL_SWAB => 'Swab Vaginal',
            self::URETHRAL_SWAB => 'Swab Uretral',
            self::THROAT_SWAB => 'Swab de Orofaringe',
            self::SPUTUM => 'Escarro',
            self::CSF => 'Líquor (LCR)',
            self::SECRETION => 'Secreção',
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
}
