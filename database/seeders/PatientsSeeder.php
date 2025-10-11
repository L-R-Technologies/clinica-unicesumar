<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientsSeeder extends Seeder
{
    public function run(): void
    {
        // Paciente 1
        $patient1 = User::create([
            'name' => 'Maria Silva Santos',
            'email' => 'maria.santos@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);

        // Paciente 2
        $patient2 = User::create([
            'name' => 'João Carlos Oliveira',
            'email' => 'joao.oliveira@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);

        $address = Address::where('street', 'Rua Itajubá')->first();

        Patient::create([
            'user_id' => $patient1->id,
            'address_id' => $address->id,
            'birth_date' => '1990-01-01',
            'ethnicity' => 'Branca',
            'sex' => 'female',
            'cpf' => '12345678901',
            'rg' => 'MG1234567',
            'phone' => '41999999999',
            'lgpd_consent_at' => now(),
        ]);

        Patient::create([
            'user_id' => $patient2->id,
            'address_id' => $address->id,
            'birth_date' => '1985-05-15',
            'ethnicity' => 'Parda',
            'sex' => 'male',
            'cpf' => '98765432100',
            'rg' => 'PR9876543',
            'phone' => '41988888888',
            'lgpd_consent_at' => now(),
        ]);
    }
}
