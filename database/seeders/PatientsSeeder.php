<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Address;

class PatientsSeeder extends Seeder
{
    public function run(): void
    {
        $patient = User::create([
            'name' => 'Paciente',
            'email' => 'paciente@email.com',
            'password' => bcrypt('123456789'),
            'role' => 'patient',
        ]);

        $address = Address::where('street', 'Rua Itajubá - Seeder')->first();

        Patient::create([
            'user_id' => $patient->id,
            'address_id' => $address->id,
            'birth_date' => '1990-01-01',
            'ethnicity' => 'Branca',
            'sex' => 'female',
            'cpf' => '12345678901',
            'rg' => 'MG1234567',
            'phone' => '(41) 99999-9999',
        ]);
    }
}
