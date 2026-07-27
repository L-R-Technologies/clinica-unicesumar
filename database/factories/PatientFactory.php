<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->patient(),
            'address_id' => null,
            'birthday' => '1990-01-01',
            'ethnicity' => 'Branca',
            'sex' => 'female',
            'cpf' => fake()->unique()->numerify('###########'),
            'rg' => fake()->numerify('#######'),
            'phone' => fake()->numerify('###########'),
            'lgpd_consent_at' => now(),
        ];
    }
}
