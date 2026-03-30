<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StudentsSeeder::class,
            TeachersSeeder::class,
            AddressesSeeder::class,
            PatientsSeeder::class,
            PatientHistoriesSeeder::class,
            SampleTypesSeeder::class,
            ExamTypesSeeder::class,
            ExamTypeFieldsSeeder::class,
            SamplesSeeder::class,
            ExamsSeeder::class,
            ExamRejectionsSeeder::class,
            ExamFeedbacksSeeder::class,
            MachinesSeeder::class,
            CalibrationsSeeder::class,
        ]);
    }
}
