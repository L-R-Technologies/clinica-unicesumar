<?php

namespace App\Livewire\Machines;

use App\Service\MachineService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class CreateMachine extends Component
{
    public $name = '';

    public $model = '';

    public $serial_number = '';

    public $location = '';

    public $calibration_range_min = null;

    public $calibration_range_max = null;

    public function save()
    {
        try {
            $machineService = app(MachineService::class);

            $machineData = [
                'name' => $this->name,
                'model' => $this->model,
                'serial_number' => $this->serial_number,
                'location' => $this->location,
                'calibration_range_min' => $this->calibration_range_min,
                'calibration_range_max' => $this->calibration_range_max,
            ];

            $validatedData = $machineService->validateMachineData($machineData);
            $machineService->createMachine($validatedData);

            session()->flash('success', 'Máquina cadastrada com sucesso!');

            return redirect()->route('machines.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao criar máquina: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.machines.create-machine');
    }
}
