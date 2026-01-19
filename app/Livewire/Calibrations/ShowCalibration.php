<?php

namespace App\Livewire\Calibrations;

use App\Models\Calibration;
use App\Service\CalibrationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class ShowCalibration extends Component
{
    public Calibration $calibration;

    public function mount(Calibration $calibration)
    {
        $this->calibration = $calibration->load(['machine', 'user']);
    }

    /**
     * Método para excluir o registro de calibração.
     */
    public function delete()
    {
        try {
            $machineId = $this->calibration->machine_id;
            $service = app(CalibrationService::class);

            // UC030: Remoção física do registro via Service
            $service->deleteCalibration($this->calibration);

            session()->flash('success', 'Registro de calibração excluído com sucesso!');

            return redirect()->to(route('machines.show', $machineId));
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao excluir: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.calibrations.show-calibration');
    }
}
