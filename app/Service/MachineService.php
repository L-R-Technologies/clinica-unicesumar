<?php

namespace App\Service;

use App\Models\Machine;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MachineService
{
    public function validateMachineData(array $data, $id = null)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => [
                'required',
                'string',
                Rule::unique('machines')->ignore($id),
            ],
            'location' => 'required|string|max:255',
            'calibration_range_min' => 'nullable|numeric',
            'calibration_range_max' => 'nullable|numeric',
            'status' => 'sometimes|string',
        ], [
            'required' => 'Preencha este campo obrigatório',
            'serial_number.unique' => 'Número de série já registrado.',
        ])->validate();
    }

    public function createMachine(array $data)
    {
        $data['status'] = 'active';

        return Machine::create($data);
    }

    public function updateMachine(Machine $machine, array $data)
    {
        return $machine->update($data);
    }

    public function deleteMachine(Machine $machine)
    {
        if ($machine->calibrations()->exists()) {
            throw new Exception('Esta máquina possui registros de calibração e não pode ser removida.');
        }

        return $machine->delete();
    }

    public function getFilteredMachines(array $filters)
    {
        $query = Machine::query();

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('model', 'like', '%'.$filters['search'].'%')
                    ->orWhere('serial_number', 'like', '%'.$filters['search'].'%');
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
}
