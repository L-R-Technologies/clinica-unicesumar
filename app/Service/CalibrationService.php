<?php

namespace App\Service;

use App\Models\Calibration;
use App\Models\Machine;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Validator;

class CalibrationService
{
    /**
     * UC026: Valida e registra uma nova calibração.
     */
    public function validateAndCreate(array $data)
    {
        $machine = Machine::findOrFail($data['machine_id']);

        if ($machine->status === 'inactive') {
            throw new Exception('Máquinas inativas não podem receber novas calibrações.');
        }

        // Cálculo automático de aprovação
        $data['status'] = ($data['value'] >= $machine->calibration_range_min &&
                          $data['value'] <= $machine->calibration_range_max)
                          ? 'approved' : 'rejected';

        return Calibration::create($this->validateData($data));
    }

    /**
     * UC029: Atualiza um registro existente (Corrige o seu erro atual).
     */
    public function updateCalibration(Calibration $calibration, array $data)
    {
        // Ao editar, também revalida o status baseado no novo valor
        $machine = $calibration->machine;
        $data['status'] = ($data['value'] >= $machine->calibration_range_min &&
                          $data['value'] <= $machine->calibration_range_max)
                          ? 'approved' : 'rejected';

        return $calibration->update($this->validateData($data));
    }

    /**
     * UC030: Exclui um registro.
     */
    public function deleteCalibration(Calibration $calibration)
    {
        return $calibration->delete();
    }

    /**
     * UC027: Exportação PDF (Corrigida para evitar erro de codificação binária).
     */
    public function exportToPdf(array $filters = [])
    {
        $calibrations = Calibration::with(['machine', 'user'])
            ->when(isset($filters['machine_id']), fn ($q) => $q->where('machine_id', $filters['machine_id']))
            ->get();

        $pdf = Pdf::loadView('pdf.calibrations', compact('calibrations'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'relatorio_calibracao_'.now()->format('d_m_Y').'.pdf');
    }

    private function validateData(array $data)
    {
        return Validator::make($data, [
            'machine_id' => 'required|exists:machines,id',
            'user_id' => 'required|exists:users,id',
            'calibration_date' => 'required|date|before_or_equal:today',
            'value' => 'required|numeric',
            'status' => 'required|in:approved,rejected',
            'observation' => 'nullable|string|max:1000',
        ])->validate();
    }
}
