<?php

namespace Database\Seeders;

use App\Models\Calibration;
use Illuminate\Database\Seeder;

class CalibrationsSeeder extends Seeder
{
    public function run(): void
    {
        $calibrations = [
            [
                'user_id' => 3, // Professor (Ricardo Martins Alves)
                'machine_id' => 1, // Analisador Hematológico
                'calibration_date' => now()->subDays(30),
                'value' => 50.5,
                'status' => 'approved',
                'observation' => 'Calibração dentro dos parâmetros normais.',
            ],
            [
                'user_id' => 3, // Professor (Ricardo Martins Alves)
                'machine_id' => 2, // Analisador Bioquímico
                'calibration_date' => now()->subDays(25),
                'value' => 100.2,
                'status' => 'approved',
                'observation' => 'Equipamento calibrado com sucesso.',
            ],
            [
                'user_id' => 3, // Professor (Ricardo Martins Alves)
                'machine_id' => 3, // Microscópio Óptico
                'calibration_date' => now()->subDays(20),
                'value' => 400.0,
                'status' => 'approved',
                'observation' => 'Calibração do microscópio realizada.',
            ],
            [
                'user_id' => 3, // Professor (Ricardo Martins Alves)
                'machine_id' => 4, // Centrífuga
                'calibration_date' => now()->subDays(15),
                'value' => 3500.5,
                'status' => 'rejected',
                'observation' => 'Valor fora do range esperado. Necessária nova calibração.',
            ],
            [
                'user_id' => 3, // Professor (Ricardo Martins Alves)
                'machine_id' => 4, // Centrífuga - Nova calibração
                'calibration_date' => now()->subDays(10),
                'value' => 3000.0,
                'status' => 'approved',
                'observation' => 'Recalibração aprovada após ajustes.',
            ],
            [
                'user_id' => 3, // Professor (Ricardo Martins Alves)
                'machine_id' => 5, // Espectrofotômetro
                'calibration_date' => now()->subDays(5),
                'value' => 650.0,
                'status' => 'approved',
                'observation' => 'Equipamento funcionando adequadamente.',
            ],
            [
                'user_id' => 3, // Professor (Ricardo Martins Alves)
                'machine_id' => 1, // Analisador Hematológico - Calibração mais recente
                'calibration_date' => now()->subDays(3),
                'value' => 51.2,
                'status' => 'approved',
                'observation' => 'Manutenção preventiva realizada.',
            ],
        ];

        foreach ($calibrations as $calibration) {
            Calibration::create($calibration);
        }
    }
}
