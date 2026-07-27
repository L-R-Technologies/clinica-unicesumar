<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Service\SampleService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SampleController extends Controller
{
    protected $sampleService;

    public function __construct(SampleService $sampleService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher,student');
        $this->sampleService = $sampleService;
    }

    public function index(Request $request): Response
    {
        $user = Auth::user();

        $filters = [
            'search' => $request->input('search', ''),
            'status' => $request->input('status', ''),
            'date' => $request->input('date', ''),
        ];

        return Inertia::render('samples/index', [
            'samples' => $this->sampleService->getFilteredSamples([
                ...$filters,
                'user_id' => $user->id,
                'user_role' => $user->role,
            ]),
            'filters' => $filters,
            'statusOptions' => $this->sampleService->getStatusOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('samples/create', [
            'patients' => $this->sampleService->getPatients()->values(),
            'sampleTypes' => $this->sampleService->getSampleTypes(),
            'statusOptions' => $this->sampleService->getStatusOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = $this->sampleService->validateSampleData($request->all());
            $this->sampleService->createSample($validatedData, Auth::id());

            return redirect()
                ->route('samples.index')
                ->with('success', 'Amostra criada com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->with('error', 'Erro ao criar amostra: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show($id): Response
    {
        $sample = Sample::with(['patient.user', 'user', 'sampleType'])->findOrFail($id);

        $this->authorize('view', $sample);

        return Inertia::render('samples/show', [
            'sample' => $sample,
        ]);
    }

    public function edit($id): Response
    {
        $sample = Sample::with(['patient.user', 'user', 'sampleType'])->findOrFail($id);

        $this->authorize('update', $sample);

        return Inertia::render('samples/edit', [
            'sample' => $sample,
            'patients' => $this->sampleService->getPatients()->values(),
            'sampleTypes' => $this->sampleService->getSampleTypesForEdit($sample->sample_type_id),
            'statusOptions' => $this->sampleService->getStatusOptions(),
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $sample = Sample::findOrFail($id);
        $this->authorize('update', $sample);

        try {
            $validatedData = $this->sampleService->validateSampleData($request->all(), $sample->id);
            $this->sampleService->updateSample($sample, $validatedData);

            return redirect()
                ->route('samples.index')
                ->with('success', 'Amostra atualizada com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->with('error', 'Erro ao atualizar amostra: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        $sample = Sample::findOrFail($id);
        $this->authorize('delete', $sample);

        try {
            $this->sampleService->deleteSample($sample);

            return redirect()
                ->route('samples.index')
                ->with('success', 'Amostra removida com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
