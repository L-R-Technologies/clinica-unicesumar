<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Exception;

class PatientAddressForm extends Component
{
    public $zip_code, $street, $number, $complement, $neighborhood, $city, $state, $country;

    public function mount($address = null)
    {
        $this->zip_code = request()->old('zip_code', $address->zip_code ?? '');
        $this->street = request()->old('street', $address->street ?? '');
        $this->number = request()->old('number', $address->number ?? '');
        $this->complement = request()->old('complement', $address->complement ?? '');
        $this->neighborhood = request()->old('neighborhood', $address->neighborhood ?? '');
        $this->city = request()->old('city', $address->city ?? '');
        $this->state = request()->old('state', $address->state ?? '');
        $this->country = request()->old('country', $address->country ?? '');
    }

    public function fetchAddress()
    {
        $cleanCep = preg_replace('/\D/', '', $this->zip_code);

        if (strlen($cleanCep) !== 8) {
            return;
        }

        try {
            $response = file_get_contents("https://viacep.com.br/ws/{$cleanCep}/json/");
            $data = json_decode($response, true);

            if (!isset($data['erro'])) {
                $this->street = $data['logradouro'] ?? '';
                $this->neighborhood = $data['bairro'] ?? '';
                $this->city = $data['localidade'] ?? '';
                $this->state = $data['uf'] ?? '';
                $this->country = 'Brasil';
                $this->complement = $data['complemento'] ?? '';

            }
        } catch (Exception $e) {
            // Em caso de erro, não faz nada - deixa o usuário preencher manualmente
        }
    }

    public function render()
    {
        return view('livewire.auth.patient-address-form');
    }
}
