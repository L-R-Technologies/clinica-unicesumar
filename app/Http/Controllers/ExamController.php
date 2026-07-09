<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamRejection;
use App\Notifications\ExamApprovedNotification;
use App\Notifications\ExamRejectedNotification;
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
        $exam = Exam::with(['user', 'patient', 'patientHistory', 'sample', 'examType.fields'])->findOrFail($id);

        return view('exam.show', compact('exam'));
    }

    public function edit($id)
    {
        $exam = Exam::with(['user', 'patient', 'patientHistory', 'sample', 'examType.fields'])->findOrFail($id);

        return view('exam.edit', compact('exam'));
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

    public function approve($id)
    {
        try {
            // Apenas professores podem aprovar exames
            if (Auth::user()->role !== 'teacher') {
                return back()->withErrors(['error' => 'Apenas professores podem aprovar exames.']);
            }

            $exam = Exam::with('user')->findOrFail($id);

            // Atualizar status do exame
            $exam->update(['status' => 'approved']);

            // Enviar notificação ao aluno que cadastrou o exame
            $exam->user->notify(new ExamApprovedNotification($exam));

            return back()->with('success', 'Exame aprovado com sucesso! Email enviado ao aluno.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Erro ao aprovar exame: '.$e->getMessage()]);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            // Apenas professores podem reprovar exames
            if (Auth::user()->role !== 'teacher') {
                return back()->withErrors(['error' => 'Apenas professores podem reprovar exames.']);
            }

            // Validar a justificativa
            $validated = $request->validate([
                'justification' => 'required|string|min:10|max:1000',
            ], [
                'justification.required' => 'A justificativa é obrigatória.',
                'justification.min' => 'A justificativa deve ter pelo menos 10 caracteres.',
                'justification.max' => 'A justificativa não pode exceder 1000 caracteres.',
            ]);

            $exam = Exam::with('user')->findOrFail($id);

            // Atualizar status do exame
            $exam->update(['status' => 'rejected']);

            // Registrar a rejeição com justificativa
            ExamRejection::create([
                'exam_id' => $exam->id,
                'user_id' => Auth::id(),
                'justification' => $validated['justification'],
            ]);

            // Enviar notificação ao aluno que cadastrou o exame com a justificativa
            $exam->user->notify(new ExamRejectedNotification($exam, $validated['justification']));

            return back()->with('success', 'Exame rejeitado com sucesso! Email enviado ao aluno com a justificativa.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Erro ao reprovar exame: '.$e->getMessage()]);
        }
    }
}
