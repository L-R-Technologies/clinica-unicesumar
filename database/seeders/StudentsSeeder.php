<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        $student1 = User::create([
            'name' => 'Lucas Henrique Souza',
            'email' => 'lucas.souza@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $student2 = User::create([
            'name' => 'Juliana Mendes Rocha',
            'email' => 'juliana.rocha@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        Student::create([
            'user_id' => $student1->id,
            'ra' => '20250001',
            'semester' => 4,
            'course' => 'Medicina',
        ]);

        Student::create([
            'user_id' => $student2->id,
            'ra' => '20250002',
            'semester' => 4,
            'course' => 'Biomedicina',
        ]);
    }
}
