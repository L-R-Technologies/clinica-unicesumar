<?php

namespace Database\Seeders;

use App\Enums\SampleType;
use App\Models\Patient;
use App\Models\Sample;
use App\Models\User;
use Illuminate\Database\Seeder;

class SamplesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pega o primeiro paciente que encontrar no banco de dados.
        $patient = Patient::first();

        // Pega o primeiro usuário (apenas como exemplo para o user_id)
        $user = User::first();

        // CORRIGIDO: Agora verifica se existe PELO MENOS UM paciente.
        if (! $patient || ! $user) {
            $this->command->warn('Não foram encontrados pacientes ou usuários suficientes. Pulando o SamplesSeeder.');

            return;
        }

        Sample::create([
            'patient_id' => 1,
            'user_id' => 2,
            'code' => '14092025-001',
            'type' => SampleType::WHOLE_BLOOD->value,
            'patient_id' => $patient->id,
            'user_id' => $user->id,
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
