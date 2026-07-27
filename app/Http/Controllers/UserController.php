<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\AddressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function edit(User $user): Response
    {
        abort_unless($user->id === Auth::id(), 403);

        $user->load('patient.address');

        return Inertia::render('profile/edit', [
            'profileUser' => $user,
            'patient' => $user->patient,
            'address' => $user->patient?->address,
        ]);
    }

    public function editPassword(User $user): Response
    {
        abort_unless($user->id === Auth::id(), 403);

        return Inertia::render('profile/password', [
            'profileUser' => $user,
        ]);
    }

    public function updateAddress(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->id === Auth::id(), 403);

        $patient = $user->patient;

        abort_if(! $patient, 403, 'Perfil de paciente não encontrado.');

        $this->addressService->updateForPatient($patient, $request->all());

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
            Log::error('Erro ao anonimizar dados do usuário', ['user_id' => $user->id, 'exception' => $e]);

            return back()->with('error', 'Ocorreu um erro ao apagar os dados. Por favor, tente novamente.');
        }
    }
}
