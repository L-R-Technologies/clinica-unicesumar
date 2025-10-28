<?php

namespace App\Livewire\ExamType;

use App\Service\ExamTypeService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CreateExamType extends Component
{
    public $name = '';
    public $description = '';

    public function save()
    {
        try {
            $examTypeService = app(ExamTypeService::class);

            $data = [
                'name' => $this->name,
                'description' => $this->description,
            ];

            $validatedData = $examTypeService->validateExamTypeData($data);
            $examTypeService->createExamType($validatedData);

            session()->flash('success', 'Tipo de exame criado com sucesso!');

            return redirect()->route('exam-type.index');

        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao criar tipo de exame: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.exam-type.create-exam-type');
    }
}
