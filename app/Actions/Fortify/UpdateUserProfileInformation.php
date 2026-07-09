<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        $rules = [
            'name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id, 'id'),
            ],
        ];

        $messages = [
            'email.unique' => 'Este e-mail já está cadastrado no sistema. Se é seu e-mail, deixe-o como está.',
        ];

        if ($user->role === 'teacher') {
            $rules['registration_number'] = ['required', 'string', 'max:10'];
            $rules['crbm'] = ['required', 'string', 'max:10'];
        } elseif ($user->role === 'student') {
            $rules['ra'] = ['required', 'string', 'max:9', Rule::unique('students', 'ra')->ignore(optional($user->student)->id, 'id')];
            $rules['course'] = ['required', 'string', 'max:100'];
            $messages['ra.unique'] = 'Este RA já está cadastrado no sistema.';
        } elseif ($user->role === 'patient') {
            if (isset($input['cpf'])) {
                $input['cpf'] = preg_replace('/\D/', '', $input['cpf']);
            }
            if (isset($input['phone'])) {
                $input['phone'] = preg_replace('/\D/', '', $input['phone']);
            }
            if (isset($input['zip_code'])) {
                $input['zip_code'] = preg_replace('/\D/', '', $input['zip_code']);
            }

            $rules = array_merge($rules, [
                'birthday' => ['required', 'date'],
                'ethnicity' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
                'sex' => ['required', 'in:male,female,other'],
                'cpf' => ['required', 'cpf', 'string', 'min:11', 'max:11', Rule::unique('patients', 'cpf')->ignore(optional($user->patient)->id, 'id')],
                'rg' => ['required', 'string', 'max:20'],
                'phone' => ['required', 'string', 'min:11', 'max:11'],
                'street' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:255'],
                'number' => ['required', 'string', 'max:20'],
                'complement' => ['nullable', 'string', 'max:100'],
                'neighborhood' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
                'city' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
                'state' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
                'country' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
                'zip_code' => ['required', 'string', 'min:8', 'max:8'],
            ]);
            $messages['cpf.unique'] = 'Este CPF já está cadastrado no sistema.';
        }

        $validator = Validator::make($input, $rules, $messages);
        $validator->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }

        if ($user->role === 'teacher' && $user->teacher) {
            $user->teacher->update([
                'registration_number' => $input['registration_number'],
                'crbm' => $input['crbm'],
            ]);
        } elseif ($user->role === 'student' && $user->student) {
            $user->student->update([
                'ra' => $input['ra'],
                'course' => $input['course'],
            ]);
        } elseif ($user->role === 'patient' && $user->patient) {
            $patient = $user->patient;
            $address = $patient->address;
            if ($address) {
                $address->update([
                    'street' => $input['street'],
                    'number' => $input['number'],
                    'complement' => $input['complement'] ?? null,
                    'neighborhood' => $input['neighborhood'],
                    'city' => $input['city'],
                    'state' => $input['state'],
                    'country' => $input['country'],
                    'zip_code' => $input['zip_code'],
                ]);
            }
            $patient->update([
                'address_id' => $address ? $address->id : null,
                'birthday' => $input['birthday'],
                'ethnicity' => $input['ethnicity'],
                'sex' => $input['sex'],
                'cpf' => $input['cpf'],
                'rg' => $input['rg'],
                'phone' => $input['phone'],
            ]);
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
