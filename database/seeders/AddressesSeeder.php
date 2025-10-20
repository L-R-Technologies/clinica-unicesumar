<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressesSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'street' => 'Rua Itajubá',
                'number' => '673',
                'complement' => 'Apto 101',
                'neighborhood' => 'Portão',
                'city' => 'Curitiba',
                'state' => 'PR',
                'country' => 'Brasil',
                'zip_code' => '87000-000',
            ],
            [
                'street' => 'Avenida Paulista',
                'number' => '1578',
                'complement' => null,
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
                'country' => 'Brasil',
                'zip_code' => '01310-200',
            ],
            [
                'street' => 'Rua das Flores',
                'number' => '123',
                'complement' => 'Casa 2',
                'neighborhood' => 'Centro',
                'city' => 'Maringá',
                'state' => 'PR',
                'country' => 'Brasil',
                'zip_code' => '87000-123',
            ],
        ];

        foreach ($addresses as $address) {
            Address::create($address);
        }
    }
}
