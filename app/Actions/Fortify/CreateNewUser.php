<?php

namespace App\Actions\Fortify;

use App\Models\Address;
use App\Models\Student;
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
        // Regras base para todos os usuários
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'role' => ['required', 'in:patient,student,teacher'],
            'password' => $this->passwordRules(),
        ];

        // Adicionar regras específicas baseadas no role
        if ($input['role'] === 'teacher') {
            $rules = array_merge($rules, [
                'registration_number' => ['required', 'string', 'max:10'],
                'crbm' => ['required', 'string', 'max:10'],
            ]);
        } elseif ($input['role'] === 'student') {
            $rules = array_merge($rules, [
                'ra' => ['required', 'string', 'max:9', Rule::unique(Student::class)],
                'course' => ['required', 'string', 'max:100'],
            ]);
        } elseif ($input['role'] === 'patient') {
            $rules = array_merge($rules, [
                // Campos do paciente
                'birth_date' => ['required', 'date'],
                'ethnicity' => ['required', 'string', 'max:100'],
                'sex' => ['required', 'in:male,female,other'],
                'cpf' => ['required', 'string', 'max:11', Rule::unique(Patient::class)],
                'rg' => ['required', 'string', 'max:20'],
                'phone' => ['required', 'string', 'max:20'],
                'lgpd_consent' => ['required', 'accepted'],

                // Campos do endereço
                'street' => ['required', 'string', 'max:255'],
                'number' => ['required', 'string', 'max:20'],
                'complement' => ['nullable', 'string', 'max:100'],
                'neighborhood' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['required', 'string', 'max:100'],
                'country' => ['required', 'string', 'max:100'],
                'zip_code' => ['required', 'string', 'max:20'],
            ]);
        }

        $validator = Validator::make($input, $rules);
        $validator->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => $input['role'],
            'password' => Hash::make($input['password']),
        ]);

        if($input['role'] === 'teacher') {
            $user->teacher()->create([
                'registration_number' => $input['registration_number'],
                'crbm' => $input['crbm'],
            ]);
        } elseif ($input['role'] === 'student') {
            $user->student()->create([
                'ra' => $input['ra'],
                'course' => $input['course'],
            ]);
        } elseif ($input['role'] === 'patient') {
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
        }

        return $user;
    }
}
