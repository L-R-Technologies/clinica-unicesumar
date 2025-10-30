<?php

namespace App\Livewire\SampleType;

use App\Models\SampleType;
use App\Service\SampleTypeService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EditSampleType extends Component
{
    public SampleType $sampleType;

    public $name;

    public $description;

    public function mount(SampleType $sampleType)
    {
        $this->sampleType = $sampleType;
        $this->name = $sampleType->name;
        $this->description = $sampleType->description;
    }

    public function save(SampleTypeService $sampleTypeService)
    {
        try {
            $data = [
                'name' => $this->name,
                'description' => $this->description,
            ];

            $sampleTypeService->updateSampleType($this->sampleType, $data);

            session()->flash('success', 'Tipo de amostra atualizado com sucesso!');

            return redirect()->route('sample-type.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao atualizar tipo de amostra: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sample-type.edit-sample-type');
    }
}
