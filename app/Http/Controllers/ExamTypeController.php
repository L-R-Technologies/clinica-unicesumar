<?php

namespace App\Http\Controllers;

use App\Models\ExamType;
use App\Models\ExamTypeField;
use App\Service\ExamTypeService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ExamTypeController extends Controller
{
    protected $examTypeService;

    public function __construct(ExamTypeService $examTypeService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
        $this->examTypeService = $examTypeService;
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
            $fields = $this->validateFields($request);

            $examType = $this->examTypeService->createExamType($validatedData);
            $this->syncFields($examType, $fields);

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
            'examType' => $examType->load('fields'),
        ]);
    }

    public function edit(ExamType $examType): Response
    {
        return Inertia::render('exam-types/edit', [
            'examType' => $examType->load('fields'),
        ]);
    }

    public function update(Request $request, ExamType $examType): RedirectResponse
    {
        try {
            $validatedData = $this->examTypeService->validateExamTypeData($request->all(), $examType->id);
            $fields = $this->validateFields($request);

            $this->examTypeService->updateExamType($examType, $validatedData);
            $this->syncFields($examType, $fields);

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

    /**
     * Valida os campos personalizados recebidos e devolve o array já validado.
     *
     * @return array<int, array<string, mixed>>
     */
    private function validateFields(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'fields' => 'nullable|array',
            'fields.*.id' => 'nullable|integer',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|string|in:int,float,string,boolean',
            'fields.*.unit' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $request->input('fields', []);
    }

    /**
     * Sincroniza os campos personalizados do tipo de exame: cria os novos,
     * atualiza os existentes e remove os que não vieram na requisição.
     *
     * @param  array<int, array<string, mixed>>  $fields
     */
    private function syncFields(ExamType $examType, array $fields): void
    {
        $existingIds = $examType->fields()->pluck('id')->toArray();
        $incomingIds = array_filter(array_column($fields, 'id'));

        $idsToDelete = array_diff($existingIds, $incomingIds);
        if (! empty($idsToDelete)) {
            ExamTypeField::whereIn('id', $idsToDelete)->delete();
        }

        foreach ($fields as $fieldData) {
            $attributes = [
                'name' => $fieldData['name'],
                'label' => $fieldData['label'],
                'field_type' => $fieldData['field_type'],
                'unit' => $fieldData['unit'] ?? null,
            ];

            if (! empty($fieldData['id'])) {
                $examType->fields()->whereKey($fieldData['id'])->update($attributes);
            } else {
                $examType->fields()->create($attributes);
            }
        }
    }
}
