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

    public $fields = [];

    public function mount(ExamType $examType)
    {
        $this->examType = $examType;
        $this->name = $examType->name;
        $this->description = $examType->description;

        // @phpstan-ignore-next-line
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

    public function addField()
    {
        $this->fields[] = [
            'id' => null,
            'name' => '',
            'label' => '',
            'field_type' => 'string',
            'unit' => '',
        ];
    }

    public function removeField($index)
    {
        if (isset($this->fields[$index]['id'])) {
            $this->fields[$index]['_delete'] = true;
        } else {
            unset($this->fields[$index]);
            $this->fields = array_values($this->fields);
        }
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

            foreach ($this->fields as $fieldData) {
                if (! empty($fieldData['_delete']) && isset($fieldData['id'])) {
                    ExamTypeField::find($fieldData['id'])?->delete();

                    continue;
                }

                if (empty($fieldData['name']) || empty($fieldData['label'])) {
                    continue;
                }

                $fieldType = $fieldData['field_type'] ?? 'string';
                if (! in_array($fieldType, ['int', 'float', 'string', 'boolean'])) {
                    $fieldType = 'string';
                }

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
