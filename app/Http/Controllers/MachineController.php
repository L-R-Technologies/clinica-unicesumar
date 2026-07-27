<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Service\CalibrationService;
use App\Service\MachineService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MachineController extends Controller
{
    protected $machineService;

    public function __construct(MachineService $machineService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
        $this->machineService = $machineService;
    }

    public function index(Request $request): Response
    {
        return Inertia::render('machines/index', [
            'machines' => $this->machineService->getFilteredMachines([
                'search' => $request->input('search', ''),
                'status' => $request->input('status', ''),
            ]),
            'filters' => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', ''),
            ],
            'statusOptions' => self::STATUS_OPTIONS,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('machines/create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = $this->machineService->validateMachineData($request->all());
            $this->machineService->createMachine($validatedData);

            return redirect()
                ->route('machines.index')
                ->with('success', 'Máquina cadastrada com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao criar máquina', ['exception' => $e]);

            return back()
                ->with('error', 'Não foi possível cadastrar a máquina. Tente novamente.')
                ->withInput();
        }
    }

    public function show(Machine $machine): Response
    {
        return Inertia::render('machines/show', [
            'machine' => $machine->load(['calibrations.user']),
        ]);
    }

    public function edit(Machine $machine): Response
    {
        return Inertia::render('machines/edit', [
            'machine' => $machine,
            'statusOptions' => self::STATUS_OPTIONS,
        ]);
    }

    public function update(Request $request, Machine $machine): RedirectResponse
    {
        try {
            $validatedData = $this->machineService->validateMachineData($request->all(), $machine->id);
            $this->machineService->updateMachine($machine, $validatedData);

            return redirect()
                ->route('machines.index')
                ->with('success', 'Dados atualizados com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao atualizar máquina', ['machine_id' => $machine->id, 'exception' => $e]);

            return back()
                ->with('error', 'Não foi possível atualizar a máquina. Tente novamente.')
                ->withInput();
        }
    }

    public function destroy(Machine $machine): RedirectResponse
    {
        try {
            $this->machineService->deleteMachine($machine);

            return redirect()
                ->route('machines.index')
                ->with('success', 'Equipamento removido com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao excluir: '.$e->getMessage());
        }
    }

    public function exportPdf(Machine $machine, CalibrationService $calibrationService): StreamedResponse
    {
        return $calibrationService->exportToPdf(['machine_id' => $machine->id]);
    }

    private const STATUS_OPTIONS = [
        'active' => 'Ativa',
        'maintenance' => 'Em manutenção',
        'inactive' => 'Inativa',
    ];
}
