<?php

namespace Database\Seeders;

use App\Models\SampleType;
use Illuminate\Database\Seeder;

class SampleTypesSeeder extends Seeder
{
    public function run(): void
    {
        $sampleTypes = [
            [
                'name' => 'Sangue Total',
                'description' => 'Amostra de sangue coletada em tubo com anticoagulante (EDTA)',
            ],
            [
                'name' => 'Soro',
                'description' => 'Amostra de soro obtida após coagulação e centrifugação do sangue',
            ],
            [
                'name' => 'Plasma',
                'description' => 'Amostra de plasma obtida de sangue com anticoagulante após centrifugação',
            ],
            [
                'name' => 'Urina',
                'description' => 'Amostra de urina coletada em frasco estéril',
            ],
            [
                'name' => 'Fezes',
                'description' => 'Amostra de fezes coletada em frasco estéril',
            ],
            [
                'name' => 'Swab Vaginal',
                'description' => 'Amostra coletada com swab da cavidade vaginal',
            ],
            [
                'name' => 'Swab Uretral',
                'description' => 'Amostra coletada com swab da uretra',
            ],
            [
                'name' => 'Swab de Orofaringe',
                'description' => 'Amostra coletada com swab da orofaringe',
            ],
            [
                'name' => 'Escarro',
                'description' => 'Amostra de escarro coletada para análise microbiológica',
            ],
            [
                'name' => 'Líquor (LCR)',
                'description' => 'Líquido cefalorraquidiano coletado por punção lombar',
            ],
            [
                'name' => 'Secreção',
                'description' => 'Amostra de secreção de diferentes origens',
            ],
        ];

        foreach ($sampleTypes as $sampleType) {
            SampleType::create($sampleType);
        }
    }
}
