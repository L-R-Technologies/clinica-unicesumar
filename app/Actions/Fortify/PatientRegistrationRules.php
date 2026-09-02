<?php

namespace App\Actions\Fortify;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Validation\Rule;

/**
 * Regras de validação do cadastro de paciente, agrupadas por etapa do
 * formulário. Compartilhadas entre o registro completo (CreateNewUser) e a
 * validação parcial de cada etapa (RegisterStepController).
 */
trait PatientRegistrationRules
{
    use PasswordValidationRules;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function accountRules(): array
    {
        return [
            'name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function personalDataRules(): array
    {
        return [
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'ethnicity' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'sex' => ['required', 'in:male,female,other'],
            'cpf' => ['required', 'cpf', 'string', 'min:11', 'max:11', Rule::unique(Patient::class)],
            'rg' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'min:10', 'max:11'],
        ];
    }

    /**
     * Logradouro/bairro/cidade aceitam números e pontuação comum,
     * ex.: "Av. Brasil", "Rua 15 de Novembro".
     *
     * @return array<string, array<int, mixed>>
     */
    protected function addressRules(): array
    {
        return [
            'street' => ['required', 'string', 'regex:/^[\pL\pN\s.,ºª°\'\-\/]+$/u', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'regex:/^[\pL\pN\s.,ºª°\'\-\/]+$/u', 'max:100'],
            'city' => ['required', 'string', 'regex:/^[\pL\pN\s.,ºª°\'\-\/]+$/u', 'max:100'],
            'state' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'country' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'zip_code' => ['required', 'string', 'min:8', 'max:8'],
            'lgpd_consent' => ['required', 'accepted'],
        ];
    }
}
