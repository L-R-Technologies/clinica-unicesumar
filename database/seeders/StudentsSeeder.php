<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        $student1 = User::create([
            'name' => 'Aluno 1',
            'email' => 'aluno1@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'student',
        ]);

        $student2 = User::create([
            'name' => 'Aluno 2',
            'email' => 'aluno2@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $student1->id,
            'ra' => '20250001',
            'course' => 'Medicina',
        ]);

        Student::create([
            'user_id' => $student2->id,
            'ra' => '20250002',
            'course' => 'Biomedicina',
        ]);
    }
}
