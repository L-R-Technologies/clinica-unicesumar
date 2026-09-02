<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Service\ExamFeedbackService;
use App\Service\ExamReferenceService;
use App\Service\ExamService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PatientExamController extends Controller
{
    protected $examService;

    protected $examFeedbackService;

    protected $examReferenceService;

    public function __construct(
        ExamService $examService,
        ExamFeedbackService $examFeedbackService,
        ExamReferenceService $examReferenceService,
    ) {
        $this->middleware('auth');
        $this->middleware('role:patient');
        $this->examService = $examService;
        $this->examFeedbackService = $examFeedbackService;
        $this->examReferenceService = $examReferenceService;
    }

    public function index(Request $request): Response
    {
        $patient = Auth::user()->patient;

        // Paciente só pode ver exames já aprovados — resultado não aprovado não é exibido.
        $exams = $this->examService->getFilteredExams([
            'search' => $request->input('search', ''),
            'status' => 'approved',
            'exam_type_id' => $request->input('exam_type_id', ''),
            'date_from' => $request->input('date_from', ''),
            'date_to' => $request->input('date_to', ''),
            'patient_id' => $patient?->id ?? 0, // 0 garante lista vazia se não houver perfil
        ]);

        return Inertia::render('my-exams/index', [
            'exams' => $exams,
            'examTypes' => $this->examService->getExamTypes(),
            'filters' => [
                'search' => $request->input('search', ''),
                'exam_type_id' => $request->input('exam_type_id', ''),
                'date_from' => $request->input('date_from', ''),
                'date_to' => $request->input('date_to', ''),
            ],
        ]);
    }

    public function show($id): Response
    {
        $patient = Auth::user()->patient;

        abort_if(! $patient, 403, 'Perfil de paciente não encontrado.');

        // Garante que o exame pertence ao paciente logado e já foi aprovado (defesa em profundidade)
        $exam = Exam::with(['patient.user', 'sample.sampleType', 'examType.fields.references', 'feedback'])
            ->where('patient_id', $patient->id)
            ->where('status', 'approved')
            ->findOrFail($id);

        return Inertia::render('my-exams/show', [
            'exam' => $exam,
            'resultReferences' => $this->examReferenceService->evaluateResults($exam),
        ]);
    }

    public function exportPdf($id)
    {
        $patient = Auth::user()->patient;

        abort_if(! $patient, 403, 'Perfil de paciente não encontrado.');

        // Garante que o exame pertence ao paciente logado e já foi aprovado (defesa em profundidade)
        $exam = Exam::with(['patient.user', 'sample.sampleType', 'examType.fields.references'])
            ->where('patient_id', $patient->id)
            ->where('status', 'approved')
            ->findOrFail($id);

        $resultReferences = $this->examReferenceService->evaluateResults($exam);

        $pdf = Pdf::loadView('patient-exams.pdf', compact('exam', 'resultReferences'));

        return $pdf->stream("exame-{$exam->id}.pdf");
    }

    public function storeFeedback(Request $request, $id): RedirectResponse
    {
        $patient = Auth::user()->patient;

        abort_if(! $patient, 403, 'Perfil de paciente não encontrado.');

        // Garante que o exame pertence ao paciente logado e já foi aprovado (defesa em profundidade)
        $exam = Exam::where('patient_id', $patient->id)
            ->where('status', 'approved')
            ->findOrFail($id);

        try {
            $validatedData = $this->examFeedbackService->validateFeedbackData($request->all());
            $this->examFeedbackService->createFeedback($exam, $validatedData);

            return back()->with('success', 'Obrigado pelo seu feedback!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
