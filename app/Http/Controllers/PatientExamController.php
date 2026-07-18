<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Service\ExamService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PatientExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
        $this->examService = $examService;
    }

    public function index(Request $request): Response
    {
        $patient = Auth::user()->patient;

        $exams = $this->examService->getFilteredExams([
            'search' => $request->input('search', ''),
            'status' => $request->input('status', ''),
            'exam_type_id' => $request->input('exam_type_id', ''),
            'date_from' => $request->input('date_from', ''),
            'date_to' => $request->input('date_to', ''),
            'patient_id' => $patient?->id ?? 0, // 0 garante lista vazia se não houver perfil
        ]);

        return Inertia::render('my-exams/index', [
            'exams' => $exams,
            'statusOptions' => $this->examService->getStatusOptions(),
            'examTypes' => $this->examService->getExamTypes(),
            'filters' => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', ''),
                'exam_type_id' => $request->input('exam_type_id', ''),
                'date_from' => $request->input('date_from', ''),
                'date_to' => $request->input('date_to', ''),
            ],
        ]);
    }

    public function exportPdf($id)
    {
        $patient = Auth::user()->patient;

        abort_if(! $patient, 403, 'Perfil de paciente não encontrado.');

        // Garante que o exame pertence ao paciente logado (defesa em profundidade)
        $exam = Exam::with(['patient.user', 'sample.sampleType', 'examType.fields'])
            ->where('patient_id', $patient->id)
            ->findOrFail($id);

        $pdf = Pdf::loadView('patient-exams.pdf', compact('exam'));

        return $pdf->stream("exame-{$exam->id}.pdf");
    }
}
