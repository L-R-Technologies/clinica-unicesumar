<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Service\ExamService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher,student');
        $this->examService = $examService;
    }

    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->input('search', ''),
            'status' => $request->input('status', ''),
            'exam_type_id' => $request->input('exam_type_id', ''),
            'date_from' => $request->input('date_from', ''),
            'date_to' => $request->input('date_to', ''),
        ];

        return Inertia::render('exams/index', [
            'exams' => $this->examService->getFilteredExams(array_merge($filters, [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
            ])),
            'filters' => $filters,
            'statusOptions' => $this->examService->getStatusOptions(),
            'examTypes' => $this->examService->getExamTypes(),
        ]);
    }

    public function create(Request $request): Response
    {
        $patientId = $request->input('patient_id');

        return Inertia::render('exams/create', [
            'patients' => $this->examService->getPatients(),
            'examTypes' => $this->examService->getExamTypes(),
            'histories' => $patientId ? $this->examService->getPatientHistories($patientId) : [],
            'samples' => $patientId ? $this->examService->getSamples($patientId) : [],
        ]);
    }

    public function store(Request $request): RedirectResponse
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
            Log::error('Erro ao criar exame', ['exception' => $e]);

            return back()
                ->with('error', 'Não foi possível criar o exame. Tente novamente.')
                ->withInput();
        }
    }

    public function show($id): Response
    {
        $exam = Exam::with([
            'user',
            'patient.user',
            'patientHistory',
            'sample.sampleType',
            'examType.fields',
            'rejections' => fn ($query) => $query->latest()->with('user'),
        ])->findOrFail($id);

        $this->authorize('view', $exam);

        return Inertia::render('exams/show', [
            'exam' => $exam,
        ]);
    }

    public function edit(Request $request, $id): Response
    {
        $exam = Exam::with([
            'user',
            'patient.user',
            'patientHistory',
            'sample.sampleType',
            'examType.fields',
        ])->findOrFail($id);

        $this->authorize('update', $exam);

        // Ao trocar o paciente (partial reload), recarrega histórico/amostras daquele paciente.
        $patientId = $request->input('patient_id', $exam->patient_id);

        return Inertia::render('exams/edit', [
            'exam' => $exam,
            'patients' => $this->examService->getPatients(),
            'examTypes' => $this->examService->getExamTypesForEdit($exam->exam_type_id),
            'histories' => $this->examService->getPatientHistories($patientId),
            'samples' => $this->examService->getSamples($patientId),
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);

        try {
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
            Log::error('Erro ao atualizar exame', ['exam_id' => $id, 'exception' => $e]);

            return back()
                ->with('error', 'Não foi possível atualizar o exame. Tente novamente.')
                ->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('delete', $exam);

        try {
            $this->examService->deleteExam($exam);

            return redirect()
                ->route('exam.index')
                ->with('success', 'Exame removido com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao remover exame', ['exam_id' => $id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível remover o exame. Tente novamente.');
        }
    }

    public function approve($id): RedirectResponse
    {
        $exam = Exam::with('user')->findOrFail($id);
        $this->authorize('approve', $exam);

        try {
            $this->examService->approveExam($exam);

            return back()->with('success', 'Exame aprovado com sucesso! Email enviado ao aluno e ao paciente.');
        } catch (Exception $e) {
            Log::error('Erro ao aprovar exame', ['exam_id' => $id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível aprovar o exame. Tente novamente.');
        }
    }

    public function reject(Request $request, $id): RedirectResponse
    {
        $exam = Exam::with('user')->findOrFail($id);
        $this->authorize('reject', $exam);

        $validated = $request->validate([
            'justification' => 'required|string|min:10|max:1000',
        ], [
            'justification.required' => 'A justificativa é obrigatória.',
            'justification.min' => 'A justificativa deve ter pelo menos 10 caracteres.',
            'justification.max' => 'A justificativa não pode exceder 1000 caracteres.',
        ]);

        try {
            $this->examService->rejectExam($exam, $validated['justification']);

            return back()->with('success', 'Exame rejeitado com sucesso! Email enviado ao aluno com a justificativa.');
        } catch (Exception $e) {
            Log::error('Erro ao reprovar exame', ['exam_id' => $id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível reprovar o exame. Tente novamente.');
        }
    }
}
