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
        $patient1 = $this->createPatientUser('Maria Silva Santos', 'maria.santos@email.com');

        // Paciente 2
        $patient2 = $this->createPatientUser('João Carlos Oliveira', 'joao.oliveira@email.com');

        // Paciente 3
        $patient3 = $this->createPatientUser('Ana Paula Costa', 'ana.costa@email.com');

        // Paciente 4
        $patient4 = $this->createPatientUser('Carlos Eduardo Ferreira', 'carlos.ferreira@email.com');

        // Paciente 5
        $patient5 = $this->createPatientUser('Fernanda Lima Silva', 'fernanda.lima@email.com');

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

    private function createPatientUser(string $name, string $email): User
    {
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('123456789'),
            'email_verified_at' => now(),
        ]);
        $user->role = 'patient';
        $user->save();

        return $user;
    }
}
