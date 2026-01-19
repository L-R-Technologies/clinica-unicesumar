<?php

namespace App\Livewire\Calibrations;

use App\Models\Machine;
use App\Service\CalibrationService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class CreateCalibration extends Component
{
    public Machine $machine;

    public $calibration_date;

    public $value;

    public $observation;

    public function mount(Machine $machine)
    {
        $this->machine = $machine;
        $this->calibration_date = now()->format('Y-m-d');
    }

    public function save()
    {
        try {
            $service = app(CalibrationService::class);

            $data = [
                'machine_id' => $this->machine->id,
                'user_id' => Auth::id(),
                'calibration_date' => $this->calibration_date,
                'value' => $this->value,
                'observation' => $this->observation,
            ];

            // O Service validará e calculará o status automaticamente
            $service->validateAndCreate($data);

            session()->flash('success', 'Calibração registrada com sucesso!');

            return redirect()->route('machines.show', $this->machine->id);
        } catch (Exception $e) {
            // Captura o erro e exibe no formulário (como o erro de status obrigatório que vimos antes)
            $this->addError('form', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.calibrations.create-calibration');
    }
}
