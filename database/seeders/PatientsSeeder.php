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

        // Paciente 3
        $patient3 = User::create([
            'name' => 'Ana Paula Costa',
            'email' => 'ana.costa@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);

        // Paciente 4
        $patient4 = User::create([
            'name' => 'Carlos Eduardo Ferreira',
            'email' => 'carlos.ferreira@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);

        // Paciente 5
        $patient5 = User::create([
            'name' => 'Fernanda Lima Silva',
            'email' => 'fernanda.lima@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);

        $address1 = Address::where('street', 'Rua Itajubá')->first();
        $address2 = Address::where('street', 'Avenida Paulista')->first();
        $address3 = Address::where('street', 'Rua das Flores')->first();

        Patient::create([
            'user_id' => $patient1->id,
            'address_id' => $address1->id,
            'birthday' => '1990-01-01',
            'ethnicity' => 'Branca',
            'sex' => 'female',
            'cpf' => '12345678901',
            'rg' => 'MG1234567',
            'phone' => '41999999999',
            'lgpd_consent_at' => now(),
        ]);

        Patient::create([
            'user_id' => $patient2->id,
            'address_id' => $address1->id,
            'birthday' => '1985-05-15',
            'ethnicity' => 'Parda',
            'sex' => 'male',
            'cpf' => '98765432100',
            'rg' => 'PR9876543',
            'phone' => '41988888888',
            'lgpd_consent_at' => now(),
        ]);

        Patient::create([
            'user_id' => $patient3->id,
            'address_id' => $address2->id,
            'birthday' => '1992-03-22',
            'ethnicity' => 'Negra',
            'sex' => 'female',
            'cpf' => '11122233344',
            'rg' => 'SP1122334',
            'phone' => '11987654321',
            'lgpd_consent_at' => now(),
        ]);

        Patient::create([
            'user_id' => $patient4->id,
            'address_id' => $address3->id,
            'birthday' => '1978-11-08',
            'ethnicity' => 'Parda',
            'sex' => 'male',
            'cpf' => '55566677788',
            'rg' => 'PR5566778',
            'phone' => '44988776655',
            'lgpd_consent_at' => now(),
        ]);

        Patient::create([
            'user_id' => $patient5->id,
            'address_id' => $address2->id,
            'birthday' => '1988-07-14',
            'ethnicity' => 'Branca',
            'sex' => 'female',
            'cpf' => '99988877766',
            'rg' => 'SP9988776',
            'phone' => '11966554433',
            'lgpd_consent_at' => now(),
        ]);
    }
}
