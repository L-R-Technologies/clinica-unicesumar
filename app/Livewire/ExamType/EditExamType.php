<?php

namespace App\Livewire\ExamType;

use App\Models\ExamType;
use App\Models\ExamTypeField;
use App\Service\ExamTypeService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EditExamType extends Component
{
    public ExamType $examType;

    public $name;

    public $description;

    // Campos personalizados existentes e novos
    public $fields = [];

    public function mount(ExamType $examType)
    {
        $this->examType = $examType;
        $this->name = $examType->name;
        $this->description = $examType->description;

        // Carrega os campos existentes
        $this->fields = $examType->fields->map(function (ExamTypeField $field) {
            return [
                'id' => $field->id,
                'name' => $field->name,
                'label' => $field->label,
                'field_type' => $field->field_type,
                'unit' => $field->unit,
            ];
        })->toArray();
    }

    /**
     * Adiciona um novo campo dinâmico
     */
    public function addField()
    {
        $this->fields[] = [
            'id' => null,
            'name' => '',
            'label' => '',
            'field_type' => 'string', // Ajustado para ENUM
            'unit' => '',
        ];
    }

    /**
     * Remove um campo — se tiver ID, será removido do banco ao salvar
     */
    public function removeField($index)
    {
        if (isset($this->fields[$index]['id'])) {
            $this->fields[$index]['_delete'] = true; // marca para exclusão
        } else {
            unset($this->fields[$index]);
            $this->fields = array_values($this->fields);
        }
    }

    /**
     * Atualiza o tipo de exame e seus campos
     */
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

            // Atualiza ou cria/exclui campos
            foreach ($this->fields as $fieldData) {
                // Se marcado para exclusão
                if (! empty($fieldData['_delete']) && isset($fieldData['id'])) {
                    ExamTypeField::find($fieldData['id'])?->delete();

                    continue;
                }

                // Ignora campos vazios
                if (empty($fieldData['name']) || empty($fieldData['label'])) {
                    continue;
                }

                // Garante que field_type esteja dentro do ENUM permitido
                $fieldType = $fieldData['field_type'] ?? 'string';
                if (! in_array($fieldType, ['int', 'float', 'string', 'boolean'])) {
                    $fieldType = 'string';
                }

                // Atualiza ou cria
                if (isset($fieldData['id']) && $fieldData['id']) {
                    $field = ExamTypeField::find($fieldData['id']);
                    if ($field) {
                        $field->update([
                            'name' => $fieldData['name'],
                            'label' => $fieldData['label'],
                            'field_type' => $fieldType,
                            'unit' => $fieldData['unit'] ?? null,
                        ]);
                    }
                } else {
                    $this->examType->fields()->create([
                        'name' => $fieldData['name'],
                        'label' => $fieldData['label'],
                        'field_type' => $fieldType,
                        'unit' => $fieldData['unit'] ?? null,
                    ]);
                }
            }

            session()->flash('success', 'Tipo de exame atualizado com sucesso!');

            return redirect()->route('exam-type.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao atualizar tipo de exame: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.exam-type.edit-exam-type');
    }
}
