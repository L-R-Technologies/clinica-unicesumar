<?php

namespace App\Http\Controllers;

use App\Models\Calibration;
use App\Models\Machine;
use App\Service\CalibrationService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalibrationController extends Controller
{
    protected $calibrationService;

    public function __construct(CalibrationService $calibrationService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
        $this->calibrationService = $calibrationService;
    }

    public function index(Request $request): \Inertia\Response
    {
        $filters = $this->extractFilters($request);

        return Inertia::render('calibrations/index', [
            'calibrations' => $this->calibrationService
                ->getFilteredCalibrationsQuery($filters)
                ->latest('calibration_date')
                ->paginate(20)
                ->withQueryString(),
            'machines' => Machine::where('status', 'active')->get(),
            'filters' => $filters,
            'statusOptions' => self::STATUS_OPTIONS,
        ]);
    }

    public function create(Machine $machine): \Inertia\Response
    {
        return Inertia::render('calibrations/create', [
            'machine' => $machine,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $machineId = $request->input('machine_id');

        try {
            $this->calibrationService->validateAndCreate([
                'machine_id' => $machineId,
                'user_id' => Auth::id(),
                'calibration_date' => $request->input('calibration_date'),
                'value' => $request->input('value'),
                'observation' => $request->input('observation'),
            ]);

            return redirect()
                ->route('machines.show', $machineId)
                ->with('success', 'Calibração registrada com sucesso!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao registrar calibração', ['machine_id' => $machineId, 'exception' => $e]);

            return back()->with('error', 'Não foi possível registrar a calibração. Tente novamente.')->withInput();
        }
    }

    public function show(Calibration $calibration): \Inertia\Response
    {
        return Inertia::render('calibrations/show', [
            'calibration' => $calibration->load(['machine', 'user']),
        ]);
    }

    public function edit(Calibration $calibration): \Inertia\Response
    {
        return Inertia::render('calibrations/edit', [
            'calibration' => $calibration->load('machine'),
        ]);
    }

    public function update(Request $request, Calibration $calibration): RedirectResponse
    {
        try {
            $this->calibrationService->updateCalibration($calibration, [
                'machine_id' => $calibration->machine_id,
                'user_id' => Auth::id(),
                'calibration_date' => $request->input('calibration_date'),
                'value' => $request->input('value'),
                'observation' => $request->input('observation'),
            ]);

            return redirect()
                ->route('calibrations.show', $calibration->id)
                ->with('success', 'Calibração atualizada com sucesso!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao atualizar calibração', ['calibration_id' => $calibration->id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível atualizar a calibração. Tente novamente.')->withInput();
        }
    }

    public function destroy(Calibration $calibration): RedirectResponse
    {
        try {
            $machineId = $calibration->machine_id;
            $this->calibrationService->deleteCalibration($calibration);

            return redirect()
                ->route('machines.show', $machineId)
                ->with('success', 'Registro de calibração excluído com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao excluir: '.$e->getMessage());
        }
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->calibrationService->getFilteredCalibrationsQuery($this->extractFilters($request));

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=calibracoes_'.now()->format('d_m_Y').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query): void {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Equipamento', 'Série', 'Data', 'Valor', 'Status', 'Responsável']);

            foreach ($query->get() as $row) {
                $date = Carbon::parse($row->calibration_date);

                fputcsv($file, [
                    $row->id,
                    $row->machine->getAttribute('name'),
                    $row->machine->getAttribute('serial_number'),
                    $date->format('d/m/Y H:i'),
                    $row->value,
                    $row->status === 'approved' ? 'Aprovada' : 'Rejeitada',
                    $row->user->getAttribute('name'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'search' => $request->input('search', ''),
            'status' => $request->input('status', ''),
            'machine_id' => $request->input('machine_id', ''),
        ];
    }

    private const STATUS_OPTIONS = [
        'approved' => 'Aprovada',
        'rejected' => 'Rejeitada',
    ];
}
