<?php

namespace App\Livewire\Machines;

use App\Models\Machine;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.base')]
class MachineIndex extends Component
{
    use WithPagination;

    /**
     * Este é o método que o botão de excluir vai chamar.
     */
    public function deleteMachine($id)
    {
        try {
            $machine = Machine::findOrFail($id);

            // UC025: Remove a máquina do banco de dados
            $machine->delete();

            session()->flash('success', 'Equipamento removido com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao excluir: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.machines.machine-index', [
            'machines' => Machine::latest()->paginate(10),
        ]);
    }
}
