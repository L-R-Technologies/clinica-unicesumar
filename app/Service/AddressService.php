<?php

namespace App\Service;

use App\Models\Address;
use App\Models\Patient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AddressService
{
    /**
     * Normaliza, valida e persiste o endereço de um paciente, criando-o ou
     * atualizando o existente.
     *
     * @param  array<string, mixed>  $input
     *
     * @throws ValidationException
     */
    public function updateForPatient(Patient $patient, array $input): Address
    {
        $validated = $this->validateAddressData($input);

        $address = $patient->address;

        if ($address) {
            $address->update($validated);

            return $address;
        }

        $address = Address::create($validated);
        $patient->update(['address_id' => $address->id]);

        return $address;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    private function validateAddressData(array $input): array
    {
        if (isset($input['zip_code'])) {
            $input['zip_code'] = preg_replace('/\D/', '', (string) $input['zip_code']);
        }

        $validator = Validator::make($input, [
            'street' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'city' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'state' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'country' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'zip_code' => ['required', 'string', 'min:8', 'max:8'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
