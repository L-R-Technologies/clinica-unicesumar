<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Service\ExamService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher,student');
        $this->examService = $examService;
    }

    public function index()
    {
        return view('exam.index-livewire');
    }

    public function create()
    {
        return view('exam.create-livewire');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->examService->validateExamData($request->all());
            $this->examService->createExam($validatedData, Auth::id());

            return redirect()
                ->route('exam.index')
                ->with('success', 'Exame criado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao criar exame: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $exam = Exam::with(['user', 'patient', 'patientHistory', 'sample'])->findOrFail($id);

        return view('exam.show', compact('exam'));
    }

    public function edit($id)
    {
        $exam = Exam::with(['user', 'patient', 'patientHistory', 'sample'])->findOrFail($id);

        $patients = $this->examService->getPatients();
        $patientHistories = $this->examService->getPatientHistories($exam->patient_id);
        $samples = $this->examService->getSamples();
        $examTypes = $this->examService->getExamTypes();
        $statusOptions = $this->examService->getStatusOptions();

        return view('exam.edit', compact('exam', 'patients', 'patientHistories', 'samples', 'examTypes', 'statusOptions'));
    }

    public function update(Request $request, $id)
    {
        try {
            $exam = Exam::findOrFail($id);

            $validatedData = $this->examService->validateExamData($request->all(), $exam->id);
            $this->examService->updateExam($exam, $validatedData);

            return redirect()
                ->route('exam.index')
                ->with('success', 'Exame atualizado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao atualizar exame: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $this->examService->deleteExam($exam);

            return redirect()
                ->route('exam.index')
                ->with('success', 'Exame removido com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover exame: '.$e->getMessage()]);
        }
    }

    public function getPatientHistories(Request $request)
    {
        $patientId = $request->get('patient_id');
        $histories = $this->examService->getPatientHistories($patientId);

        return response()->json($histories);
    }
}
