<?php

namespace App\Http\Controllers;

use App\Models\ExamType;
use App\Service\ExamTypeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExamTypeController extends Controller
{
    protected $examTypeService;

    public function __construct(ExamTypeService $examTypeService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher,student');
        $this->examTypeService = $examTypeService;
    }

    public function index()
    {
        return view('examType.index-livewire');
    }

    public function create()
    {
        return view('examType.create_livewire');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->examTypeService->validateExamTypeData($request->all());
            $this->examTypeService->createExamType($validatedData, Auth::id());

            return redirect()
                ->route('exam-type.index')
                ->with('success', 'Tipo de exame criado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao criar tipo de exame: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $examType = ExamType::findOrFail($id);

        return view('examType.show', compact('examType'));
    }

    public function edit($id)
    {
        $examType = ExamType::findOrFail($id);

        return view('examType.edit', compact('examType'));
    }

    public function update(Request $request, $id)
    {
        try {
            $examType = ExamType::findOrFail($id);

            $validatedData = $this->examTypeService->validateExamTypeData($request->all(), $examType->id);
            $this->examTypeService->updateExamType($examType, $validatedData);

            return redirect()
                ->route('exam-type.index')
                ->with('success', 'Tipo de exame atualizado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao atualizar tipo de exame: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $examType = ExamType::findOrFail($id);
            $this->examTypeService->deleteExamType($examType);

            return redirect()
                ->route('exam-type.index')
                ->with('success', 'Tipo de exame removido com sucesso!');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover tipo de exame: ' . $e->getMessage()]);
        }
    }
}
