<?php

namespace App\Http\Livewire\Samples;

use App\Models\Sample;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class EditSample extends Component
{
    public Sample $sample;

    public ?int $patient_id = null;

    public string $type = '';

    public string $date = '';

    public string $status = '';

    public string $location = '';

    protected function rules()
    {
        return [
            'type' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:under_review,stored,discarded'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function mount(Sample $sample)
    {
        $this->sample = $sample;
        $this->patient_id = $sample->patient_id;
        $this->type = $sample->type;
        $this->date = $sample->date->format('Y-m-d');
        $this->status = $sample->status;
        $this->location = $sample->location;
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
