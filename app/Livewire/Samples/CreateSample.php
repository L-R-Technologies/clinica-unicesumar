<?php

namespace App\Livewire\Samples;

use App\Service\SampleService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class CreateSample extends Component
{
    public ?int $patient_id = null;

    public ?int $sample_type_id = null;

    public string $date = '';

    public string $status = 'under review';

    public string $location = '';

    public $patients = [];

    public $sampleTypes = [];

    public function mount()
    {
        $sampleService = app(SampleService::class);
        $this->patients = $sampleService->getPatients();
        $this->sampleTypes = $sampleService->getSampleTypes();
        $this->date = now()->format('Y-m-d');
    }

    public function save()
    {
        try {
            $sampleService = app(SampleService::class);

            $sampleData = [
                'patient_id' => $this->patient_id,
                'sample_type_id' => $this->sample_type_id,
                'date' => $this->date,
                'status' => $this->status,
                'location' => $this->location,
            ];

            $validatedData = $sampleService->validateSampleData($sampleData);
            $sampleService->createSample($validatedData, Auth::id());

            session()->flash('success', 'Amostra criada com sucesso!');

            return redirect()->route('samples.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao criar amostra: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.samples.create-samples');
    }
}
