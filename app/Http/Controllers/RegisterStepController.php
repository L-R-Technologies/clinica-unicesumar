<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PatientRegistrationRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterStepController extends Controller
{
    use PatientRegistrationRules;

    private const ACCOUNT_STEP = 1;

    private const PERSONAL_DATA_STEP = 2;

    /**
     * Valida parcialmente o cadastro de paciente, apenas com as regras da
     * etapa informada, para dar feedback antes do envio final. A última
     * etapa é validada pelo próprio registro (CreateNewUser).
     */
    public function validateStep(Request $request, int $step): RedirectResponse
    {
        abort_unless(
            in_array($step, [self::ACCOUNT_STEP, self::PERSONAL_DATA_STEP], true),
            404,
        );

        foreach (['cpf', 'phone', 'zip_code'] as $field) {
            if ($request->filled($field)) {
                $request->merge([
                    $field => preg_replace('/\D/', '', $request->string($field)->toString()),
                ]);
            }
        }

        $rules = $step === self::ACCOUNT_STEP
            ? $this->accountRules()
            : $this->personalDataRules();

        Validator::make($request->all(), $rules)->validate();

        return back();
    }
}
