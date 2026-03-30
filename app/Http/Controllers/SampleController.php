<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Service\SampleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SampleController extends Controller
{
    protected $sampleService;

    public function __construct(SampleService $sampleService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher,student');
        $this->sampleService = $sampleService;
    }

    public function index()
    {
        return view('samples.index-livewire');
    }

    public function create()
    {
        return view('samples.create-livewire');
    }

    public function store(Request $request)
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
                ->withErrors(['error' => 'Erro ao criar amostra: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $sample = Sample::with(['patient.user', 'user', 'sampleType'])->findOrFail($id);

        return view('samples.show', compact('sample'));
    }

    public function edit($id)
    {
        $sample = Sample::with(['patient.user', 'user', 'sampleType'])->findOrFail($id);

        return view('samples.edit', compact('sample'));
    }

    public function update(Request $request, $id)
    {
        try {
            $sample = Sample::findOrFail($id);

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
                ->withErrors(['error' => 'Erro ao atualizar amostra: '.$e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $sample = Sample::findOrFail($id);
            $this->sampleService->deleteSample($sample);

            return redirect()
                ->route('samples.index')
                ->with('success', 'Amostra removida com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover amostra: '.$e->getMessage()]);
        }
    }
}
