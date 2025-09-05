<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SamplesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('samples')->insert([
            [
                'patient_id' => 1,
                'user_id' => 2, // profissional que coletou
                'code' => 'SMP000001',
                'type' => 'Sangue',
                'date' => now()->subDays(3),
                'location' => 'Laboratório Central',
                'status' => 'under_review',
                'notified' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_id' => 2,
                'user_id' => 3,
                'code' => 'SMP000002',
                'type' => 'Urina',
                'date' => now()->subDays(1),
                'location' => 'Laboratório Norte',
                'status' => 'stored',
                'notified' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
