<?php

namespace App\Livewire\ExamType;

use App\Models\ExamType;
use App\Service\ExamTypeService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EditExamType extends Component
{
    public ExamType $examType;

    public $name;
    public $description;

    public function mount(ExamType $examType)
    {
        $this->examType = $examType;
        $this->name = $examType->name;
        $this->description = $examType->description;
    }

    public function save()
    {
        try {
            $examTypeService = app(ExamTypeService::class);

            $data = [
                'name' => $this->name,
                'description' => $this->description,
            ];

            $validatedData = $examTypeService->validateExamTypeData($data, $this->examType->id);
            $examTypeService->updateExamType($this->examType, $validatedData);

            session()->flash('success', 'Tipo de exame atualizado com sucesso!');
            return redirect()->route('exam-type.index');

        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao atualizar tipo de exame: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.exam-type.edit-exam-type');
    }
}
