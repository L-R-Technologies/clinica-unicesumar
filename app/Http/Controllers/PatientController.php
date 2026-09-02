<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Service\PatientService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientController extends Controller
{
    private const PATIENTS_PER_PAGE = 20;

    public function __construct(protected PatientService $patientService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher,student');
    }

    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->input('search', ''),
        ];

        return Inertia::render('patients/index', [
            'patients' => $this->patientService->getFilteredPatients($filters, self::PATIENTS_PER_PAGE),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('patients/create', [
            'sexOptions' => PatientService::SEX_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = $this->patientService->validatePatientData($request->all());
            $this->patientService->createPatient($validatedData, $validatedData['lgpd_term']);

            return redirect()
                ->route('patients.index')
                ->with('success', 'Paciente cadastrado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao cadastrar paciente', ['exception' => $e]);

            return back()
                ->with('error', 'Não foi possível cadastrar o paciente. Tente novamente.')
                ->withInput();
        }
    }

    public function show(Patient $patient): Response
    {
        return Inertia::render('patients/show', [
            'patient' => $patient->load(['user', 'address']),
            'sexOptions' => PatientService::SEX_OPTIONS,
        ]);
    }

    public function edit(Patient $patient): Response
    {
        return Inertia::render('patients/edit', [
            'patient' => $patient->load(['user', 'address']),
            'sexOptions' => PatientService::SEX_OPTIONS,
        ]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        try {
            $validatedData = $this->patientService->validatePatientData($request->all(), $patient);
            $this->patientService->updatePatient($patient, $validatedData, $validatedData['lgpd_term'] ?? null);

            return redirect()
                ->route('patients.show', $patient->id)
                ->with('success', 'Paciente atualizado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao atualizar paciente', ['patient_id' => $patient->id, 'exception' => $e]);

            return back()
                ->with('error', 'Não foi possível atualizar o paciente. Tente novamente.')
                ->withInput();
        }
    }

    /**
     * Exibe o termo LGPD assinado (imagem ou PDF) armazenado no disco privado.
     */
    public function lgpdTerm(Patient $patient): StreamedResponse
    {
        $disk = Storage::disk(PatientService::LGPD_TERM_DISK);

        abort_if(! $patient->lgpd_term_path || ! $disk->exists($patient->lgpd_term_path), 404, 'Termo LGPD não encontrado.');

        return $disk->response($patient->lgpd_term_path);
    }
}
