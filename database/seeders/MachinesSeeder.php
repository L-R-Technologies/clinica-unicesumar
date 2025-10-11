<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;

class MachinesSeeder extends Seeder
{
    public function run(): void
    {
        $machines = [
            [
                'name' => 'Analisador Hematológico',
                'model' => 'BC-5000',
                'serial_number' => 'BC5000-2023-001',
                'location' => 'Laboratório de Hematologia',
                'calibration_range_min' => 0.1,
                'calibration_range_max' => 99.9,
                'status' => 'active',
            ],
            [
                'name' => 'Analisador Bioquímico',
                'model' => 'AU-480',
                'serial_number' => 'AU480-2023-002',
                'location' => 'Laboratório de Bioquímica',
                'calibration_range_min' => 1.0,
                'calibration_range_max' => 1000.0,
                'status' => 'active',
            ],
            [
                'name' => 'Microscópio Óptico',
                'model' => 'BX43',
                'serial_number' => 'BX43-2023-003',
                'location' => 'Laboratório de Microscopia',
                'calibration_range_min' => 10.0,
                'calibration_range_max' => 1000.0,
                'status' => 'active',
            ],
            [
                'name' => 'Centrífuga',
                'model' => 'CF-15R',
                'serial_number' => 'CF15R-2023-004',
                'location' => 'Laboratório Central',
                'calibration_range_min' => 500.0,
                'calibration_range_max' => 15000.0,
                'status' => 'calibrating',
            ],
            [
                'name' => 'Espectrofotômetro',
                'model' => 'UV-1800',
                'serial_number' => 'UV1800-2023-005',
                'location' => 'Laboratório de Análises Especiais',
                'calibration_range_min' => 190.0,
                'calibration_range_max' => 1100.0,
                'status' => 'inactive',
            ],
        ];

        foreach ($machines as $machine) {
            Machine::create($machine);
        }
    }
}
