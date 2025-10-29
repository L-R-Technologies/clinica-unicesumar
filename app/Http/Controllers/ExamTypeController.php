<?php

namespace App\Http\Controllers;

use App\Models\ExamType;
use App\Models\ExamTypeField;
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
        return view('exam-type.index-livewire');
    }

    public function create()
    {
        return view('exam-type.create-livewire');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->examTypeService->validateExamTypeData($request->all());
            $examType = $this->examTypeService->createExamType($validatedData, Auth::id());

            // --- Criação dos campos personalizados ---
            if ($request->has('fields') && is_array($request->fields)) {
                foreach ($request->fields as $fieldData) {
                    if (!empty($fieldData['name']) && !empty($fieldData['label'])) {
                        $examType->fields()->create([
                            'name' => $fieldData['name'],
                            'label' => $fieldData['label'],
                            'field_type' => $fieldData['field_type'] ?? 'text',
                            'unit' => $fieldData['unit'] ?? null,
                        ]);
                    }
                }
            }

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
        $examType = ExamType::with('fields')->findOrFail($id);

        return view('exam-type.show', compact('examType'));
    }

    public function edit($id)
    {
        $examType = ExamType::with('fields')->findOrFail($id);

        return view('exam-type.edit', compact('examType'));
    }

    public function update(Request $request, $id)
    {
        try {
            $examType = ExamType::findOrFail($id);

            $validatedData = $this->examTypeService->validateExamTypeData($request->all(), $examType->id);
            $this->examTypeService->updateExamType($examType, $validatedData);

            // --- Atualização dos campos personalizados ---
            if ($request->has('fields') && is_array($request->fields)) {
                $existingIds = $examType->fields()->pluck('id')->toArray();
                $incomingIds = array_filter(array_column($request->fields, 'id'));

                // Exclui campos removidos
                $toDelete = array_diff($existingIds, $incomingIds);
                if (!empty($toDelete)) {
                    ExamTypeField::whereIn('id', $toDelete)->delete();
                }

                // Atualiza ou cria novos campos
                foreach ($request->fields as $fieldData) {
                    if (isset($fieldData['id'])) {
                        $field = ExamTypeField::find($fieldData['id']);
                        if ($field) {
                            $field->update([
                                'name' => $fieldData['name'],
                                'label' => $fieldData['label'],
                                'field_type' => $fieldData['field_type'] ?? 'text',
                                'unit' => $fieldData['unit'] ?? null,
                            ]);
                        }
                    } else {
                        if (!empty($fieldData['name']) && !empty($fieldData['label'])) {
                            $examType->fields()->create([
                                'name' => $fieldData['name'],
                                'label' => $fieldData['label'],
                                'field_type' => $fieldData['field_type'] ?? 'text',
                                'unit' => $fieldData['unit'] ?? null,
                            ]);
                        }
                    }
                }
            }

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
