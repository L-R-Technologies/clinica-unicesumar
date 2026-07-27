<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeachersSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = new User([
            'name' => 'Ricardo Martins Alves',
            'email' => 'ricardo.alves@email.com',
            'password' => bcrypt('123456789'),
            'email_verified_at' => now(),
        ]);
        $teacher->role = 'teacher';
        $teacher->save();

        Teacher::create([
            'user_id' => $teacher->id,
            'registration_number' => 'T2025001',
            'professional_license' => 'CRBM12345',
        ]);
    }
}
