<?php

namespace App\Http\Controllers;

use App\Models\PatientHistory;
use App\Service\PatientHistoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PatientHistoryController extends Controller
{
    protected $patientHistoryService;

    public function __construct(PatientHistoryService $patientHistoryService)
    {
        $this->middleware('auth');
        $this->patientHistoryService = $patientHistoryService;
    }

    public function index(Request $request)
    {
        $query = PatientHistory::with(['patient', 'user']);

        // Filtro por paciente (nome)
        if ($request->filled('patient')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->patient.'%');
            });
        }

        // Filtro por profissional (nome)
        if ($request->filled('professional')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->professional.'%');
            });
        }

        // Filtro por data da coleta
        if ($request->filled('recorded_at')) {
            $query->whereDate('recorded_at', $request->recorded_at);
        }

        $anamneses = $query->latest()->paginate(10)->withQueryString();

        return view('patient-history.index', compact('anamneses'));
    }

    public function create()
    {
        return view('patient-history.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->patientHistoryService->validateData($request->all());

            $validatedData['user_id'] = auth()->id();
            $validatedData['patient_id'] = $request->patient_id ?? 1;

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
                ->withErrors(['error' => 'Erro ao criar anamnese: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $anamnese = PatientHistory::with(['patient', 'user'])->findOrFail($id);

        return view('patient-history.show', compact('anamnese'));
    }

    public function edit($id)
    {
        $anamnese = PatientHistory::findOrFail($id);

        return view('patient-history.edit', compact('anamnese'));
    }

    public function update(Request $request, $id)
    {
        try {
            $anamnesis = PatientHistory::findOrFail($id);

            $validatedData = $this->patientHistoryService->validateData($request->all());

            $this->patientHistoryService->update($anamnesis, $validatedData);

            return redirect()
                ->route('patient-histories.index')
                ->with('success', 'Anamnese atualizada com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao atualizar anamnese: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $anamnesis = PatientHistory::findOrFail($id);

            $this->patientHistoryService->delete($anamnesis);

            return redirect()
                ->route('patient-histories.index')
                ->with('success', 'Anamnese removida com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover anamnese: '.$e->getMessage()]);
        }
    }
}
