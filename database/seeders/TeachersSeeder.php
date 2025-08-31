<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;

class TeachersSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::create([
            'name' => 'Professor',
            'email' => 'professor@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'teacher',
            'email_verified_at' => now()
        ]);

        Teacher::create([
            'user_id' => $teacher->id,
            'registration_number' => 'T2025001',
            'crbm' => 'CRBM12345',
        ]);
    }
}
