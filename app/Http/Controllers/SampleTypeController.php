<?php

namespace App\Http\Controllers;

use App\Models\SampleType;
use App\Service\SampleTypeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SampleTypeController extends Controller
{
    protected $sampleTypeService;

    public function __construct(SampleTypeService $sampleTypeService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
        $this->sampleTypeService = $sampleTypeService;
    }

    public function index()
    {
        return view('sample-type.index-livewire');
    }

    public function create()
    {
        return view('sample-type.create-livewire');
    }

    public function store(Request $request)
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
                ->withErrors(['error' => 'Erro ao criar tipo de amostra: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $sampleType = SampleType::findOrFail($id);

        return view('sample-type.show', compact('sampleType'));
    }

    public function edit($id)
    {
        $sampleType = SampleType::findOrFail($id);

        return view('sample-type.edit-livewire', compact('sampleType'));
    }

    public function update(Request $request, $id)
    {
        try {
            $sampleType = SampleType::findOrFail($id);

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
                ->withErrors(['error' => 'Erro ao atualizar tipo de amostra: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $sampleType = SampleType::findOrFail($id);
            $this->sampleTypeService->deleteSampleType($sampleType);

            return redirect()
                ->route('sample-type.index')
                ->with('success', 'Tipo de amostra removido com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover tipo de amostra: '.$e->getMessage()]);
        }
    }
}
