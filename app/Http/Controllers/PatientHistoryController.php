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
        $this->middleware('role:teacher,student');
        $this->patientHistoryService = $patientHistoryService;
    }

    public function index()
    {
        return view('patient-history.index-livewire');
    }

    public function create()
    {
        return view('patient-history.create-livewire');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->patientHistoryService->validateData($request->all());
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
        $anamnese = PatientHistory::with(['patient.user', 'user'])->findOrFail($id);

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
