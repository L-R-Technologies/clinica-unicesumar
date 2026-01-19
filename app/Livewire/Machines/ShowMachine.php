<?php

namespace App\Livewire\Machines;

use App\Models\Machine;
use App\Service\CalibrationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class ShowMachine extends Component
{
    public Machine $machine;

    public function mount(Machine $machine)
    {
        $this->machine = $machine->load(['calibrations.user']);
    }

    public function exportCsv()
    {
        return app(CalibrationService::class)->exportToCsv(['machine_id' => $this->machine->id]);
    }

    public function exportPdf()
    {
        return app(CalibrationService::class)->exportToPdf(['machine_id' => $this->machine->id]);
    }

    public function render()
    {
        return view('livewire.machines.show-machine');
    }
}
