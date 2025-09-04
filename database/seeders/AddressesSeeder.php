<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

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
