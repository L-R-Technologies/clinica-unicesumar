<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Service\ExamService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PatientExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
        $this->examService = $examService;
    }

    public function index()
    {
        return view('patient-exams.index-livewire');
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
