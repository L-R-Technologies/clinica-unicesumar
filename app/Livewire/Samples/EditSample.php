<?php

namespace App\Livewire\Samples;

use App\Models\Sample;
use App\Models\SampleType;
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

    protected function rules()
    {
        return [
            'sample_type_id' => ['required', 'integer', 'exists:sample_types,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:under review,stored,discarded'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function mount(Sample $sample)
    {
        $this->sample = $sample;
        $this->patient_id = $sample->patient_id;
        $this->sample_type_id = $sample->sample_type_id;
        $this->date = $sample->date->format('Y-m-d');
        $this->status = $sample->status;
        $this->location = $sample->location;
        $this->sampleTypes = SampleType::orderBy('name')->get();
    }

    public function save()
    {
        $validatedData = $this->validate();

        $this->sample->update($validatedData);

        session()->flash('success', 'Amostra atualizada com sucesso!');

        return redirect()->route('samples.index');
    }

    public function render()
    {
        return view('livewire.samples.edit-sample');
    }
}
