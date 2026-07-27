<?php

namespace App\Service;

use App\Models\Calibration;
use App\Models\Machine;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class CalibrationService
{
    public function getFilteredCalibrationsQuery(array $filters): Builder
    {
        return Calibration::with(['machine', 'user'])
            ->when(! empty($filters['search']), function ($q) use ($filters) {
                $q->whereHas('machine', fn ($m) => $m->where('name', 'like', '%'.$filters['search'].'%'));
            })
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['machine_id']), fn ($q) => $q->where('machine_id', $filters['machine_id']));
    }

    public function validateAndCreate(array $data)
    {
        $machine = Machine::findOrFail($data['machine_id']);

        if ($machine->status === 'inactive') {
            throw new Exception('Máquinas inativas não podem receber novas calibrações.');
        }

        $data['status'] = ($data['value'] >= $machine->calibration_range_min &&
                          $data['value'] <= $machine->calibration_range_max)
                          ? 'approved' : 'rejected';

        return Calibration::create($this->validateData($data));
    }

    public function updateCalibration(Calibration $calibration, array $data)
    {
        $machine = Machine::findOrFail($calibration->machine_id);

        $data['status'] = ($data['value'] >= $machine->calibration_range_min &&
                          $data['value'] <= $machine->calibration_range_max)
                          ? 'approved' : 'rejected';

        return $calibration->update($this->validateData($data));
    }

    public function deleteCalibration(Calibration $calibration)
    {
        return $calibration->delete();
    }

    public function exportToPdf(array $filters = [])
    {
        $calibrations = Calibration::with(['machine', 'user'])
            ->when(isset($filters['machine_id']), fn ($q) => $q->where('machine_id', $filters['machine_id']))
            ->latest('calibration_date')
            ->get();

        $machine = isset($filters['machine_id'])
            ? Machine::find($filters['machine_id'])
            : null;

        $pdf = Pdf::loadView('pdf.calibrations', compact('calibrations', 'machine'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'relatorio_calibracao_'.now()->format('d_m_Y').'.pdf');
    }

    private function validateData(array $data)
    {
        return Validator::make($data, [
            'machine_id' => 'required|exists:machines,id',
            'user_id' => 'required|exists:users,id',
            'calibration_date' => 'required|date|before_or_equal:now',
            'value' => 'required|numeric',
            'status' => 'required|in:approved,rejected',
            'observation' => 'nullable|string|max:1000',
        ])->validate();
    }
}
