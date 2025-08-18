<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        if ($user->role === 'teacher') {
            $rules['registration_number'] = ['required', 'string', 'max:10'];
            $rules['crbm'] = ['required', 'string', 'max:10'];
        } elseif ($user->role === 'student') {
            $rules['ra'] = ['required', 'string', 'max:9', Rule::unique('students')->ignore(optional($user->student)->id)];
            $rules['course'] = ['required', 'string', 'max:100'];
        } elseif ($user->role === 'patient') {
            $rules = array_merge($rules, [
                'birth_date' => ['required', 'date'],
                'ethnicity' => ['required', 'string', 'max:100'],
                'sex' => ['required', 'in:male,female,other'],
                'cpf' => ['required', 'string', 'max:11', Rule::unique('patients')->ignore(optional($user->patient)->id)],
                'rg' => ['required', 'string', 'max:20'],
                'phone' => ['required', 'string', 'max:20'],
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
        $validator->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
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
            if ($user->patient->address) {
                $user->patient->address->update([
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
            $user->patient->update([
                'address_id' => $user->patient->address ? $user->patient->address->id : null,
                'birth_date' => $input['birth_date'],
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
