<?php

namespace App\Livewire\Calibrations;

use App\Models\Calibration;
use App\Service\CalibrationService;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class EditCalibration extends Component
{
    public Calibration $calibration;

    public $calibration_date;

    public $value;

    public $status;

    public $observation;

    public function mount(Calibration $calibration)
    {
        $this->calibration = $calibration;
        $this->calibration_date = $calibration->calibration_date->format('Y-m-d');
        $this->value = $calibration->value;
        $this->status = $calibration->status;
        $this->observation = $calibration->observation;
    }

    public function save()
    {
        try {
            $service = app(CalibrationService::class);

            $data = [
                'machine_id' => $this->calibration->machine_id,
                'user_id' => auth()->id(),
                'calibration_date' => $this->calibration_date,
                'value' => $this->value,
                'observation' => $this->observation,
            ];

            $service->updateCalibration($this->calibration, $data);

            session()->flash('success', 'Calibração atualizada com sucesso!');

            return redirect()->route('calibrations.show', $this->calibration->id);
        } catch (Exception $e) {
            $this->addError('form', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.calibrations.edit-calibration');
    }
}
