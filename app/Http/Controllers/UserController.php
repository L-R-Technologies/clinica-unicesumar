<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function edit(User $user): Response
    {
        $user->load('patient.address');

        return Inertia::render('profile/edit', [
            'profileUser' => $user,
            'patient' => $user->patient,
            'address' => $user->patient?->address,
        ]);
    }

    public function editPassword(User $user): Response
    {
        return Inertia::render('profile/password', [
            'profileUser' => $user,
        ]);
    }

    public function updateAddress(Request $request, User $user): RedirectResponse
    {
        $patient = $user->patient;

        abort_if(! $patient, 403, 'Perfil de paciente não encontrado.');

        $input = $request->all();
        if (isset($input['zip_code'])) {
            $input['zip_code'] = preg_replace('/\D/', '', $input['zip_code']);
        }

        $validated = validator($input, [
            'street' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'city' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'state' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'country' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'max:100'],
            'zip_code' => ['required', 'string', 'min:8', 'max:8'],
        ])->validate();

        $address = $patient->address;

        if ($address) {
            $address->update($validated);
        } else {
            $address = Address::create($validated);
            $patient->update(['address_id' => $address->id]);
        }

        return back()->with('success', 'Endereço atualizado com sucesso!');
    }

    public function anonymize(Request $request): RedirectResponse
    {
        $request->validate([
            'confirmation' => ['required', 'in:CONFIRMAR'],
        ]);

        $user = User::findOrFail(Auth::id());

        try {
            DB::transaction(function () use ($user) {
                $user->anonymizePersonalData();
            });

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('status', 'Seus dados foram apagados com sucesso. Obrigado por utilizar nossos serviços.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro ao apagar os dados. Por favor, tente novamente.');
        }
    }
}
