<?php

namespace App\Http\Controllers;

use App\Models\PatientHistory;
use App\Service\PatientHistoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PatientHistoryController extends Controller
{
    protected $patientHistoryService;

    public function __construct(PatientHistoryService $patientHistoryService)
    {
        $this->middleware('auth');
        //$this->middleware('active'); // pode habilitar se necessário
        $this->patientHistoryService = $patientHistoryService;
    }

    public function index()
    {
        $anamneses = PatientHistory::with(['patient', 'user'])->latest()->paginate(10);

        return view('anamneses.index', compact('anamneses'));
    }

    public function create()
    {
        return view('anamneses.create');
    }

public function store(Request $request)
{
    try {
        // Valida os dados recebidos
        $validatedData = $this->patientHistoryService->validateData($request->all());

        // Define o usuário logado
        $validatedData['user_id'] = auth()->id();

        // Define o paciente (ajuste conforme seu fluxo)
        $validatedData['patient_id'] = $request->patient_id ?? 1;

        // Cria a anamnese
        $this->patientHistoryService->create($validatedData);

        // Redireciona para a listagem com mensagem de sucesso
        return redirect()
            ->route('anamneses.index')
            ->with('success', 'Anamnese criada com sucesso!');
    } catch (ValidationException $e) {
        return back()
            ->withErrors($e->errors())
            ->withInput();
    } catch (Exception $e) {
        return back()
            ->withErrors(['error' => 'Erro ao criar anamnese: ' . $e->getMessage()])
            ->withInput();
    }
}

    public function show($id)
    {
        $anamnesis = PatientHistory::with(['patient', 'user'])->findOrFail($id);

        return view('anamneses.show', compact('anamnesis'));
    }

    public function edit($id)
    {
        $anamnesis = PatientHistory::findOrFail($id);

        return view('anamneses.edit', compact('anamnesis'));
    }

    public function update(Request $request, $id)
    {
        try {
            $anamnesis = PatientHistory::findOrFail($id);

            $validatedData = $this->patientHistoryService->validateData($request->all());

            $this->patientHistoryService->update($anamnesis, $validatedData);

            return redirect()
                ->route('anamneses.index')
                ->with('success', 'Anamnese atualizada com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao atualizar anamnese: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $anamnesis = PatientHistory::findOrFail($id);

            $this->patientHistoryService->delete($anamnesis);

            return redirect()
                ->route('anamneses.index')
                ->with('success', 'Anamnese removida com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover anamnese: ' . $e->getMessage()]);
        }
    }
}
