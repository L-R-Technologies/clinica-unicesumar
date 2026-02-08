<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AnonymizeUserData extends Component
{
    public $showFirstConfirmation = false;

    public $showSecondConfirmation = false;

    public $confirmationText = '';

    public function openFirstConfirmation()
    {
        $this->showFirstConfirmation = true;
        $this->showSecondConfirmation = false;
        $this->confirmationText = '';
    }

    public function closeFirstConfirmation()
    {
        $this->showFirstConfirmation = false;
        $this->confirmationText = '';
    }

    public function proceedToSecondConfirmation()
    {
        $this->showFirstConfirmation = false;
        $this->showSecondConfirmation = true;
        $this->confirmationText = '';
    }

    public function closeSecondConfirmation()
    {
        $this->showSecondConfirmation = false;
        $this->confirmationText = '';
    }

    public function anonymizeData()
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);

        if ($this->confirmationText !== 'CONFIRMAR') {
            session()->flash('error', 'Digite "CONFIRMAR" para prosseguir com a exclusão.');

            return;
        }

        try {
            DB::beginTransaction();

            $user->anonymizePersonalData();

            DB::commit();

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            session()->flash('status', 'Seus dados foram apagados com sucesso. Obrigado por utilizar nossos serviços.');

            return redirect()->route('welcome');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Ocorreu um erro ao apagar os dados. Por favor, tente novamente. Erro: '.$e->getMessage());
            $this->showSecondConfirmation = false;
            $this->confirmationText = '';
        }
    }

    public function render()
    {
        return view('livewire.auth.anonymize-user-data');
    }
}
