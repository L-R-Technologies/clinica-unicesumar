<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressesSeeder extends Seeder
{
    public function run(): void
    {
        Address::create([
            'street' => 'Rua Itajubá - Seeder',
            'number' => '673',
            'complement' => 'Apto 101',
            'neighborhood' => 'Portão',
            'city' => 'Curitiba',
            'state' => 'PR',
            'country' => 'Brasil',
            'zip_code' => '87000-000',
        ]);
    }
}
