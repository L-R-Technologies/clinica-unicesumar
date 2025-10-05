<?php

namespace Database\Seeders;

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
            'patient_id' => $patient->id, // Usa o ID real do primeiro paciente encontrado
            'user_id' => $user->id,
            'code' => 'SMP000001',
            'type' => 'Sangue',
            'date' => now()->subDays(3),
            'location' => 'Laboratório Central',
            'status' => 'under_review',
            'notified' => false,
        ]);

        // Removida a criação da segunda amostra, pois só temos um paciente no seeder.
        // Se precisar de mais amostras, primeiro adicione mais pacientes no PatientsSeeder.
    }
}
