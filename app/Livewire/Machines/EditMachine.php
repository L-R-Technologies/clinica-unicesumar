<?php

namespace App\Livewire\Machines;

use App\Models\Machine;
use App\Service\MachineService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class EditMachine extends Component
{
    public Machine $machine;

    public $name;

    public $model;

    public $serial_number;

    public $location;

    public $status;

    public $calibration_range_min;

    public $calibration_range_max;

    public function mount(Machine $machine)
    {
        $this->machine = $machine;
        $this->name = $machine->name;
        $this->model = $machine->model;
        $this->serial_number = $machine->serial_number;
        $this->location = $machine->location;
        $this->status = $machine->status;
        $this->calibration_range_min = $machine->calibration_range_min;
        $this->calibration_range_max = $machine->calibration_range_max;
    }

    public function save()
    {
        try {
            $service = app(MachineService::class);

            $data = [
                'name' => $this->name,
                'model' => $this->model,
                'serial_number' => $this->serial_number,
                'location' => $this->location,
                'status' => $this->status,
                'calibration_range_min' => $this->calibration_range_min,
                'calibration_range_max' => $this->calibration_range_max,
            ];

            $validated = $service->validateMachineData($data, $this->machine->id);
            $service->updateMachine($this->machine, $validated);

            session()->flash('success', 'Dados atualizados com sucesso!');

            return redirect()->route('machines.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao atualizar: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.machines.edit-machine');
    }
}
