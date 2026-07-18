<?php

namespace App\Http\Controllers;

use App\Models\PatientHistory;
use App\Service\PatientHistoryService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PatientHistoryController extends Controller
{
    protected $patientHistoryService;

    public function __construct(PatientHistoryService $patientHistoryService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher,student');
        $this->patientHistoryService = $patientHistoryService;
    }

    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->input('search', ''),
            'date' => $request->input('date', ''),
        ];

        return Inertia::render('patient-histories/index', [
            'patientHistories' => $this->patientHistoryService->getFilteredHistories(Auth::user(), $filters),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('patient-histories/create', [
            'patients' => $this->patientHistoryService->getPatients(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = array_merge($request->all(), ['user_id' => Auth::id()]);

            $validatedData = $this->patientHistoryService->validateData($data);
            $this->patientHistoryService->create($validatedData);

            return redirect()
                ->route('patient-histories.index')
                ->with('success', 'Anamnese criada com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->with('error', 'Erro ao criar anamnese: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show($id): Response
    {
        $patientHistory = PatientHistory::with(['patient.user', 'user'])->findOrFail($id);

        return Inertia::render('patient-histories/show', [
            'patientHistory' => $patientHistory,
        ]);
    }

    public function edit($id): Response
    {
        $patientHistory = PatientHistory::with(['patient.user', 'user'])->findOrFail($id);

        return Inertia::render('patient-histories/edit', [
            'patientHistory' => $patientHistory,
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $patientHistory = PatientHistory::findOrFail($id);

            // Paciente e responsável não são editáveis: preserva os valores originais.
            $data = array_merge($request->all(), [
                'patient_id' => $patientHistory->patient_id,
                'user_id' => $patientHistory->user_id,
            ]);

            $validatedData = $this->patientHistoryService->validateData($data);
            $this->patientHistoryService->update($patientHistory, $validatedData);

            return redirect()
                ->route('patient-histories.index')
                ->with('success', 'Anamnese atualizada com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->with('error', 'Erro ao atualizar anamnese: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $patientHistory = PatientHistory::findOrFail($id);

            $this->patientHistoryService->delete($patientHistory);

            return redirect()
                ->route('patient-histories.index')
                ->with('success', 'Anamnese removida com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
