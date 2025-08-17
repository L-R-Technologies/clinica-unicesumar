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

        $validator = Validator::make($input, [
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

            // Campos para teacher
            'registration_number' => ['nullable', 'string', 'max:10'],
            'crbm' => ['nullable', 'string', 'max:10'],

            // Campos para student
            'ra' => ['nullable', 'string', 'max:9', Rule::unique(Student::class)],
            'course' => ['nullable', 'string', 'max:100'],

            // Campos para address
            'street' => ['nullable', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],

            // Campos para patient
            'birth_date' => ['nullable', 'date'],
            'ethnicity' => ['nullable', 'string', 'max:100'],
            'sex' => ['nullable', 'in:male,female,other'],
            'cpf' => ['nullable', 'string', 'max:11', Rule::unique(Patient::class)],
            'rg' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $validator->sometimes([
            'registration_number',
            'crbm'
        ], 'required|string', function ($input) {
            return $input->role === 'teacher';
        });

        $validator->sometimes([
            'ra',
            'course'
        ], 'required|string', function ($input) {
            return $input->role === 'student';
        });

        $validator->sometimes([
            'birth_date',
            'ethnicity',
            'sex',
            'cpf',
            'rg',
            'phone',

            'street',
            'number',
            'neighborhood',
            'city',
            'state',
            'country',
            'zip_code'
        ], 'required', function ($input) {
            return $input->role === 'patient';
        });

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
            ]);
        }

        return $user;
    }
}
