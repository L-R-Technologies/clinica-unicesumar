<?php

namespace App\Livewire\Samples;

use App\Models\Sample;
use App\Service\SampleService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class EditSample extends Component
{
    public Sample $sample;

    public ?int $patient_id = null;

    public ?int $sample_type_id = null;

    public string $date = '';

    public string $status = '';

    public string $location = '';

    public $sampleTypes = [];

    public function mount(Sample $sample)
    {
        $sampleService = app(SampleService::class);

        $this->sample = $sample;
        $this->patient_id = $sample->patient_id;
        $this->sample_type_id = $sample->sample_type_id;
        $this->date = $sample->date->format('Y-m-d');
        $this->status = $sample->status;
        $this->location = $sample->location;

        $this->sampleTypes = $sampleService->getSampleTypes();
    }

    public function save()
    {
        try {
            $sampleService = app(SampleService::class);

            $sampleData = [
                'sample_type_id' => $this->sample_type_id,
                'date' => $this->date,
                'status' => $this->status,
                'location' => $this->location,
                'patient_id' => $this->patient_id,
            ];

            $validatedData = $sampleService->validateSampleData($sampleData, $this->sample->id);
            $sampleService->updateSample($this->sample, $validatedData);

            session()->flash('success', 'Amostra atualizada com sucesso!');

            return redirect()->route('samples.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao atualizar amostra: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.samples.edit-sample');
    }
}
