<?php

namespace Database\Seeders;

use App\Enums\SampleType;
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
            'code' => '14092025-001',
            'type' => SampleType::WHOLE_BLOOD->value,
            'date' => now()->subDays(3),
            'location' => 'Laboratório Central',
            'status' => 'under_review',
            'notified' => false,
        ]);

        Sample::create([
            'patient_id' => 1,
            'user_id' => 3,
            'code' => '14092025-002',
            'type' => SampleType::URINE->value,
            'date' => now()->subDays(1),
            'location' => 'Laboratório Norte',
            'status' => 'stored',
            'notified' => true,
        ]);

        Sample::create([
            'patient_id' => 1,
            'user_id' => 2,
            'code' => '14092025-003',
            'type' => SampleType::SERUM->value,
            'date' => now(),
            'location' => 'Laboratório Central',
            'status' => 'under_review',
            'notified' => false,
        ]);
    }
}
