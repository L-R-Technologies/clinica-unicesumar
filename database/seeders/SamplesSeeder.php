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
        $samples = [];
        $rawSamples = [
            [1, 3, 1, now()->subDays(5), 'Geladeira 1', 'stored', true],
            [1, 1, 2, now()->subDays(5), 'Geladeira 2', 'stored', true],
            [1, 2, 4, now()->subDays(2), 'Geladeira 1', 'stored', true],
            [2, 3, 1, now()->subDays(1), 'Geladeira 2', 'under review', false],
            [2, 1, 3, now()->subDays(1), 'Geladeira 1', 'under review', false],
            [2, 2, 5, now()->subDays(1), 'Geladeira 2', 'under review', false],
            [2, 3, 4, now()->subDays(1), 'Geladeira 1', 'stored', true],
            [3, 1, 1, now()->subDays(3), 'Geladeira 2', 'stored', true],
            [3, 2, 2, now()->subDays(3), 'Geladeira 1', 'stored', true],
            [4, 3, 1, now()->subDays(4), 'Geladeira 2', 'stored', true],
            [4, 1, 2, now()->subDays(4), 'Geladeira 1', 'stored', true],
            [4, 2, 4, now()->subDays(4), 'Geladeira 2', 'stored', true],
            [5, 3, 1, now()->subDays(6), 'Geladeira 1', 'stored', true],
            [5, 1, 2, now()->subDays(6), 'Geladeira 2', 'stored', true],
            [5, 2, 4, now()->subDays(6), 'Geladeira 1', 'stored', true],
            [3, 1, 1, now()->subDays(3), 'Geladeira 2', 'stored', true],
            [4, 2, 4, now()->subDays(4), 'Geladeira 1', 'stored', true],
        ];

        $dateCount = [];
        foreach ($rawSamples as $raw) {
            $date = $raw[3]->format('dmY');
            if (! isset($dateCount[$date])) {
                $dateCount[$date] = 1;
            } else {
                $dateCount[$date]++;
            }
            $seq = str_pad($dateCount[$date], 3, '0', STR_PAD_LEFT);
            $samples[] = [
                'patient_id' => $raw[0],
                'user_id' => $raw[1],
                'sample_type_id' => $raw[2],
                'code' => $date.'-'.$seq,
                'date' => $raw[3],
                'location' => $raw[4],
                'status' => $raw[5],
                'notified' => $raw[6],
            ];
        }

        foreach ($samples as $sample) {
            Sample::create($sample);
        }
    }
}
