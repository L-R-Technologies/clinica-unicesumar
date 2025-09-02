<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Exception;

class PatientAddressForm extends Component
{
    public $zip_code, $street, $number, $complement, $neighborhood, $city, $state, $country;

    public function mount($address = null)
    {
        if ($address) {
            $this->zip_code = $this->formatCep($address->zip_code ?? '');
            $this->street = $address->street ?? '';
            $this->number = $address->number ?? '';
            $this->complement = $address->complement ?? '';
            $this->neighborhood = $address->neighborhood ?? '';
            $this->city = $address->city ?? '';
            $this->state = $address->state ?? '';
            $this->country = $address->country ?? '';
        }
    }

    private function formatCep($cep)
    {
        $clean = preg_replace('/\D/', '', $cep);
        if (strlen($clean) === 8) {
            return substr($clean, 0, 5) . '-' . substr($clean, 5);
        }
        return $cep;
    }

    public function fetchAddress($cep)
    {
        $cleanCep = preg_replace('/\D/', '', $cep);

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
