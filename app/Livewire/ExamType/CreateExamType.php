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

    // Campos personalizados
    public $fields = [
        ['name' => '', 'label' => '', 'field_type' => 'string', 'unit' => ''], // 'string' respeita o ENUM
    ];

    /**
     * Adiciona um novo campo dinâmico na lista
     */
    public function addField()
    {
        $this->fields[] = ['name' => '', 'label' => '', 'field_type' => 'string', 'unit' => ''];
    }

    /**
     * Remove um campo dinâmico pelo índice
     */
    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields); // Reorganiza os índices
    }

    /**
     * Salva o tipo de exame e seus campos personalizados
     */
    public function save()
    {
        try {
            $examTypeService = app(ExamTypeService::class);

            $data = [
                'name' => $this->name,
                'description' => $this->description,
            ];

            $validatedData = $examTypeService->validateExamTypeData($data);
            $examType = $examTypeService->createExamType($validatedData);

            // Cria os campos associados
            foreach ($this->fields as $field) {
                if (! empty($field['name']) && ! empty($field['label'])) {
                    // Garante que o field_type esteja dentro do ENUM permitido
                    $fieldType = $field['field_type'] ?? 'string';
                    if (! in_array($fieldType, ['int', 'float', 'string', 'boolean'])) {
                        $fieldType = 'string';
                    }

                    $examType->fields()->create([
                        'name' => $field['name'],
                        'label' => $field['label'],
                        'field_type' => $fieldType,
                        'unit' => $field['unit'] ?? null,
                    ]);
                }
            }

            session()->flash('success', 'Tipo de exame criado com sucesso!');

            return redirect()->route('exam-type.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao criar tipo de exame: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.exam-type.create-exam-type');
    }
}
