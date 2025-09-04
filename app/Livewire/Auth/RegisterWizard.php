<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Auth\Events\Registered;
use Exception;

class RegisterWizard extends Component
{
    public $step = 1;
    public $name, $email, $password, $password_confirmation;
    public $birth_date, $sex, $cpf, $rg, $ethnicity, $phone;
    public $zip_code, $street, $number, $neighborhood, $complement, $city, $state, $country;
    public $lgpd_consent = false;

    protected $rules = [
        // Step 1
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        // Step 2
        'birth_date' => 'required|date|before:today',
        'sex' => 'required|in:male,female,other',
        'cpf' => 'required|cpf|string|min:14|max:14|unique:patients,cpf',
        'rg' => 'required|string|max:20',
        'ethnicity' => 'required|string|max:100',
        'phone' => 'required|string|min:15|max:15',
        // Step 3
        'zip_code' => 'required|string|min:9|max:9',
        'street' => 'required|string|max:255',
        'number' => 'required|string|max:10',
        'neighborhood' => 'required|string|max:100',
        'complement' => 'nullable|string|max:100',
        'city' => 'required|string|max:100',
        'state' => 'required|string|max:50',
        'country' => 'required|string|max:50',
        'lgpd_consent' => 'accepted',
    ];

    protected $messages = [
        'lgpd_consent.accepted' => 'Você deve aceitar os termos da LGPD para continuar o cadastro.',
        'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
        'email.unique' => 'Este e-mail já está cadastrado no sistema.',
        'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
        'zip_code.max' => 'O CEP deve ter no máximo 9 caracteres.',
        'cpf.max' => 'O CPF deve ter no máximo 14 caracteres.',
        'phone.max' => 'O telefone deve ter no máximo 15 caracteres.',
    ];

    public function render()
    {
        return view('livewire.auth.register-wizard');
    }

    public function updatedZipCode($value)
    {
        $cleanCep = preg_replace('/\D/', '', $value);

        if (strlen($cleanCep) === 8) {
            $this->fetchAddressByCep($cleanCep);
        }
    }

    private function fetchAddressByCep($cep)
    {
        try {
            $response = file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
            $data = json_decode($response, true);

            if (!isset($data['erro'])) {
                $this->street = $data['logradouro'] ?? '';
                $this->neighborhood = $data['bairro'] ?? '';
                $this->city = $data['localidade'] ?? '';
                $this->state = $data['uf'] ?? '';
                $this->country = 'Brasil';

                if (!empty($data['complemento'])) {
                    $this->complement = $data['complemento'];
                }
            }
        } catch (Exception $e) {
            // Em caso de erro, não faz nada - deixa o usuário preencher manualmente
        }
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validateOnlyStep1();
        } elseif ($this->step === 2) {
            $this->validateOnlyStep2();
        }

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function register()
    {
        $this->validate();

        $createNewUser = new CreateNewUser();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'birth_date' => $this->birth_date,
            'sex' => $this->sex,
            'cpf' => $this->cleanCpf($this->cpf),
            'rg' => $this->rg,
            'ethnicity' => $this->ethnicity,
            'phone' => $this->cleanPhone($this->phone),
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'zip_code' => $this->cleanCep($this->zip_code),
            'lgpd_consent' => $this->lgpd_consent,
        ];

        $user = $createNewUser->create($userData);

        event(new Registered($user));
        return redirect()->route('login')->with(
            'success',
            'Cadastro realizado com sucesso! Verifique seu email para confirmar sua conta.'
        );
    }

    private function cleanCpf($cpf)
    {
        return preg_replace('/\D/', '', $cpf);
    }

    private function cleanPhone($phone)
    {
        return preg_replace('/\D/', '', $phone);
    }

    private function cleanCep($cep)
    {
        return preg_replace('/\D/', '', $cep);
    }

    private function validateOnlyStep1()
    {
        $this->validate([
            'name' => $this->rules['name'],
            'email' => $this->rules['email'],
            'password' => $this->rules['password'],
        ]);
    }

    private function validateOnlyStep2()
    {
        $this->validate([
            'birth_date' => $this->rules['birth_date'],
            'sex' => $this->rules['sex'],
            'cpf' => $this->rules['cpf'],
            'rg' => $this->rules['rg'],
            'ethnicity' => $this->rules['ethnicity'],
            'phone' => $this->rules['phone'],
        ]);
    }
}
