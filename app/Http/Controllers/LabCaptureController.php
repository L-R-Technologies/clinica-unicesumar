<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\LabCapture;
use App\Models\Machine;
use App\Service\ExamService;
use App\Service\LabCapture\LabCaptureService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LabCaptureController extends Controller
{
    private const FILTER_KEYS = ['search', 'status', 'machine_id', 'date_from', 'date_to'];

    public function __construct(
        private readonly LabCaptureService $labCaptureService,
        private readonly ExamService $examService,
    ) {
        $this->middleware('auth');
        $this->middleware('role:teacher,student');
    }

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', LabCapture::class);

        $filters = $this->filtersFrom($request);

        return Inertia::render('lab-captures/index', [
            'captures' => $this->labCaptureService->getFilteredCaptures($filters),
            'filters' => $filters,
            'statusOptions' => $this->labCaptureService->getStatusOptions(),
            'machines' => Machine::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Request $request, LabCapture $labCapture): Response
    {
        $this->authorize('view', $labCapture);

        $labCapture->load(['machine', 'labAgent', 'exam.patient.user', 'exam.examType', 'reviewer']);

        $patientId = (int) $request->input('patient_id', 0);
        $examTypeId = (int) $request->input('exam_type_id', $labCapture->analysis['exam_type_id'] ?? 0);

        return Inertia::render('lab-captures/show', [
            'capture' => $labCapture,
            'examTypes' => $this->labCaptureService->getExamTypesWithFields(),
            'patients' => $this->examService->getPatients(),
            'histories' => $patientId ? $this->examService->getPatientHistories($patientId) : [],
            'samples' => $patientId ? $this->examService->getSamples($patientId) : [],
            'linkableExams' => $patientId
                ? $this->labCaptureService->getLinkableExams(Auth::user(), $patientId, $examTypeId ?: null)
                : [],
            'canReview' => Auth::user()->can('review', $labCapture),
            'canDiscard' => Auth::user()->can('discard', $labCapture),
            'canRetry' => Auth::user()->can('retry', $labCapture),
        ]);
    }

    public function link(Request $request, LabCapture $labCapture): RedirectResponse
    {
        $this->authorize('review', $labCapture);

        try {
            $data = $this->labCaptureService->validateLinkData($request->all());
            $existingExam = $data['mode'] === LabCaptureService::LINK_MODE_EXISTING
                ? Exam::with('examType')->findOrFail((int) $data['exam_id'])
                : null;

            if ($existingExam) {
                $this->authorize('update', $existingExam);
            }

            $exam = $this->labCaptureService->linkToExam($labCapture, Auth::user(), $data, $existingExam);

            return redirect()
                ->route('exam.show', $exam->id)
                ->with('success', 'Resultado vinculado ao exame com sucesso!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao vincular captura a exame', ['lab_capture_id' => $labCapture->id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível vincular o resultado. Tente novamente.');
        }
    }

    public function retry(LabCapture $labCapture): RedirectResponse
    {
        $this->authorize('retry', $labCapture);

        try {
            $this->labCaptureService->retryAnalysis($labCapture);

            return back()->with('success', 'Captura reenviada para análise da IA.');
        } catch (Exception $e) {
            Log::error('Erro ao reanalisar captura', ['lab_capture_id' => $labCapture->id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível reanalisar a captura. Tente novamente.');
        }
    }

    public function discard(LabCapture $labCapture): RedirectResponse
    {
        $this->authorize('discard', $labCapture);

        $this->labCaptureService->discard($labCapture, Auth::user());

        return redirect()
            ->route('lab-captures.index')
            ->with('success', 'Captura descartada.');
    }

    /**
     * @return array<string, string>
     */
    private function filtersFrom(Request $request): array
    {
        $filters = [];

        foreach (self::FILTER_KEYS as $key) {
            $filters[$key] = (string) $request->input($key, '');
        }

        return $filters;
    }
}
