<?php

namespace App\Actions\Fortify;

use App\Models\Address;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            // Campos do paciente
            'birth_date' => ['required', 'date'],
            'ethnicity' => ['required', 'string', 'max:100'],
            'sex' => ['required', 'in:male,female,other'],
            'cpf' => ['required', 'cpf', 'string', 'min:11', 'max:11', Rule::unique(Patient::class)],
            'rg' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'min:11', 'max:11'],
            'lgpd_consent' => ['required', 'accepted'],
            // Campos do endereço
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'zip_code' => ['required', 'string', 'min:8', 'max:8'],
        ];

        if (isset($input['cpf'])) {
            $input['cpf'] = preg_replace('/\D/', '', $input['cpf']);
        }
        if (isset($input['phone'])) {
            $input['phone'] = preg_replace('/\D/', '', $input['phone']);
        }
        if (isset($input['zip_code'])) {
            $input['zip_code'] = preg_replace('/\D/', '', $input['zip_code']);
        }

        $validator = Validator::make($input, $rules);
        $validator->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => 'patient',
            'password' => Hash::make($input['password']),
        ]);

        $address = Address::create([
            'street' => $input['street'],
            'number' => $input['number'],
            'complement' => $input['complement'] ?? null,
            'neighborhood' => $input['neighborhood'],
            'city' => $input['city'],
            'state' => $input['state'],
            'country' => $input['country'],
            'zip_code' => $input['zip_code'],
        ]);

        $user->patient()->create([
            'address_id' => $address->id,
            'birth_date' => $input['birth_date'],
            'ethnicity' => $input['ethnicity'],
            'sex' => $input['sex'],
            'cpf' => $input['cpf'],
            'rg' => $input['rg'],
            'phone' => $input['phone'],
            'lgpd_consent_at' => now(),
        ]);

        return $user;
    }
}
