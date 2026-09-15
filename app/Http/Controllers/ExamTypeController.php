<?php

namespace App\Http\Controllers;

use App\Models\ExamType;
use App\Service\ExamTypeFieldService;
use App\Service\ExamTypeService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ExamTypeController extends Controller
{
    protected $examTypeService;

    protected $examTypeFieldService;

    public function __construct(ExamTypeService $examTypeService, ExamTypeFieldService $examTypeFieldService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
        $this->examTypeService = $examTypeService;
        $this->examTypeFieldService = $examTypeFieldService;
    }

    public function index(Request $request): Response
    {
        return Inertia::render('exam-types/index', [
            'examTypes' => $this->examTypeService->getFilteredExamTypes([
                'search' => $request->input('search', ''),
            ]),
            'filters' => [
                'search' => $request->input('search', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('exam-types/create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = $this->examTypeService->validateExamTypeData($request->all());
            $fields = $this->examTypeFieldService->validateFields($request->all());

            $examType = $this->examTypeService->createExamType($validatedData);
            $this->examTypeFieldService->syncFields($examType, $fields);

            return redirect()
                ->route('exam-type.index')
                ->with('success', 'Tipo de exame criado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao criar tipo de exame', ['exception' => $e]);

            return back()
                ->with('error', 'Não foi possível criar o tipo de exame. Tente novamente.')
                ->withInput();
        }
    }

    public function show(ExamType $examType): Response
    {
        return Inertia::render('exam-types/show', [
            'examType' => $examType->load('fields.references'),
        ]);
    }

    public function edit(ExamType $examType): Response
    {
        return Inertia::render('exam-types/edit', [
            'examType' => $examType->load('fields.references'),
        ]);
    }

    public function update(Request $request, ExamType $examType): RedirectResponse
    {
        try {
            $validatedData = $this->examTypeService->validateExamTypeData($request->all(), $examType->id);
            $fields = $this->examTypeFieldService->validateFields($request->all());

            $this->examTypeService->updateExamType($examType, $validatedData);
            $this->examTypeFieldService->syncFields($examType, $fields);

            return redirect()
                ->route('exam-type.index')
                ->with('success', 'Tipo de exame atualizado com sucesso!');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao atualizar tipo de exame', ['exam_type_id' => $examType->id, 'exception' => $e]);

            return back()
                ->with('error', 'Não foi possível atualizar o tipo de exame. Tente novamente.')
                ->withInput();
        }
    }

    public function toggleStatus(ExamType $examType): RedirectResponse
    {
        try {
            $this->examTypeService->toggleStatus($examType);
            $status = $examType->fresh()->is_active ? 'ativado' : 'desativado';

            return back()->with('success', "Tipo de exame {$status} com sucesso!");
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao alterar status: '.$e->getMessage());
        }
    }

    public function destroy(ExamType $examType): RedirectResponse
    {
        try {
            $this->examTypeService->deleteExamType($examType);

            return redirect()
                ->route('exam-type.index')
                ->with('success', 'Tipo de exame desativado com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao remover tipo de exame: '.$e->getMessage());
        }
    }
}
