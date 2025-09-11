<?php

namespace Database\Seeders;

use App\Models\Sample;
use Illuminate\Database\Seeder;

class SamplesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sample::create([
            'patient_id' => 1,
            'user_id' => 2,
            'code' => 'SMP000001',
            'type' => 'Sangue',
            'date' => now()->subDays(3),
            'location' => 'Laboratório Central',
            'status' => 'under_review',
            'notified' => false,
        ]);

        Sample::create([
            'patient_id' => 1,
            'user_id' => 3,
            'code' => 'SMP000002',
            'type' => 'Urina',
            'date' => now()->subDays(1),
            'location' => 'Laboratório Norte',
            'status' => 'stored',
            'notified' => true,
        ]);
    }
}
