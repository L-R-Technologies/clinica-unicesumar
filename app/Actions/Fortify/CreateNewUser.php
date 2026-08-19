<?php

namespace App\Actions\Fortify;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PatientRegistrationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $rules = array_merge(
            $this->accountRules(),
            $this->personalDataRules(),
            $this->addressRules(),
        );

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

        $user = DB::transaction(function () use ($input): User {
            $user = new User([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);
            $user->role = 'patient';
            $user->save();

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
                'birthday' => $input['birthday'],
                'ethnicity' => $input['ethnicity'],
                'sex' => $input['sex'],
                'cpf' => $input['cpf'],
                'rg' => $input['rg'],
                'phone' => $input['phone'],
                'lgpd_consent_at' => now(),
            ]);

            return $user;
        });

        $user->sendEmailVerificationNotification();

        return $user;
    }
}
