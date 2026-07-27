<?php

namespace App\Http\Controllers;

use App\Models\SampleType;
use App\Service\SampleTypeService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SampleTypeController extends Controller
{
    protected $sampleTypeService;

    public function __construct(SampleTypeService $sampleTypeService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
        $this->sampleTypeService = $sampleTypeService;
    }

    public function index(Request $request): Response
    {
        return Inertia::render('sample-types/index', [
            'sampleTypes' => $this->sampleTypeService->getFilteredSampleTypes([
                'search' => $request->input('search', ''),
            ]),
            'filters' => [
                'search' => $request->input('search', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('sample-types/create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = $this->sampleTypeService->validateSampleTypeData($request->all());
            $this->sampleTypeService->createSampleType($validatedData);

            return redirect()
                ->route('sample-type.index')
                ->with('success', 'Tipo de amostra criado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->with('error', 'Erro ao criar tipo de amostra: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show(SampleType $sampleType): Response
    {
        return Inertia::render('sample-types/show', [
            'sampleType' => $sampleType,
        ]);
    }

    public function edit(SampleType $sampleType): Response
    {
        return Inertia::render('sample-types/edit', [
            'sampleType' => $sampleType,
        ]);
    }

    public function update(Request $request, SampleType $sampleType): RedirectResponse
    {
        try {
            $validatedData = $this->sampleTypeService->validateSampleTypeData($request->all(), $sampleType->id);
            $this->sampleTypeService->updateSampleType($sampleType, $validatedData);

            return redirect()
                ->route('sample-type.index')
                ->with('success', 'Tipo de amostra atualizado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->with('error', 'Erro ao atualizar tipo de amostra: '.$e->getMessage())
                ->withInput();
        }
    }

    public function toggleStatus(SampleType $sampleType): RedirectResponse
    {
        try {
            $this->sampleTypeService->toggleStatus($sampleType);
            $status = $sampleType->fresh()->is_active ? 'ativado' : 'desativado';

            return back()->with('success', "Tipo de amostra {$status} com sucesso!");
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao alterar status: '.$e->getMessage());
        }
    }

    public function destroy(SampleType $sampleType): RedirectResponse
    {
        try {
            $this->sampleTypeService->deleteSampleType($sampleType);

            return redirect()
                ->route('sample-type.index')
                ->with('success', 'Tipo de amostra desativado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao remover tipo de amostra: '.$e->getMessage());
        }
    }
}
