<?php

namespace Database\Factories;

use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Machine>
 */
class MachineFactory extends Factory
{
    protected $model = Machine::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Coagulômetro',
            'model' => 'CL-1',
            'serial_number' => fake()->unique()->numerify('########'),
            'location' => 'Laboratório de Hematologia',
            'calibration_range_min' => 0.1,
            'calibration_range_max' => 99.9,
            'status' => 'active',
        ];
    }
}
