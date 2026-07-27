<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ];
    }

    /**
     * 'role' e 'active' não são mass assignable; são atribuídos explicitamente.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (User $user): void {
            $user->role ??= 'patient';
            $user->active ??= true;
        });
    }

    public function teacher(): static
    {
        return $this->afterMaking(fn (User $user) => $user->role = 'teacher');
    }

    public function student(): static
    {
        return $this->afterMaking(fn (User $user) => $user->role = 'student');
    }

    public function patient(): static
    {
        return $this->afterMaking(fn (User $user) => $user->role = 'patient');
    }

    public function unverified(): static
    {
        return $this->afterMaking(fn (User $user) => $user->email_verified_at = null);
    }
}
