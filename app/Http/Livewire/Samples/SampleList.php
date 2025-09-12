<?php

namespace App\Http\Livewire\Samples;

use App\Models\Patient;
use App\Models\Sample;
use Livewire\Component;
use Livewire\WithPagination;

class SampleList extends Component
{
    use WithPagination;

    public Sample $sample;

    public bool $showModal = false;

    public bool $isEditing = false;

    public string $search = '';

    protected function rules()
    {
        return [
            'sample.patient_id' => ['required', 'integer', 'exists:patients,id'],
            'sample.type' => ['required', 'string', 'max:100'],
            'sample.date' => ['required', 'date'],
            'sample.status' => ['required', 'in:under_review,stored,discarded'],
        ];
    }

    public function mount()
    {
        $this->sample = new Sample();
    }

    public function create()
    {
        $this->isEditing = false;
        $this->sample = new Sample();
        // CORREÇÃO 1: Atribuindo um objeto Carbon, não uma string.
        $this->sample->date = now();
        $this->sample->status = 'under_review';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function edit(Sample $sample)
    {
        $this->isEditing = true;
        $this->sample = $sample;
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if (! $this->isEditing) {
            $lastSample = Sample::latest('id')->first();
            // CORREÇÃO 2: Lógica simplificada para obter o próximo ID.
            $nextId = $lastSample ? $lastSample->id + 1 : 1;

            $this->sample->code = 'SMP-'.str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
        }

        $this->sample->save();

        $this->showModal = false;
        session()->flash('success', 'Amostra salva com sucesso!');
    }

    public function delete(Sample $sample)
    {
        $sample->delete();
        session()->flash('success', 'Amostra deletada com sucesso!');
    }

    public function render()
    {
        $search = $this->search;

        $samples = Sample::with('patient.user')
            ->where(function ($query) use ($search) {
                $query->where('code', 'like', '%'.$search.'%')
                    ->orWhereHas('patient.user', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%'.$search.'%');
                    });
            })
            ->orderBy('date', 'desc')
            ->paginate(10);

        $patients = Patient::with('user')->get()->sortBy('user.name');

        return view('livewire.samples.sample-list', [
            'samples' => $samples,
            'patients' => $patients,
        ]);
    }
}
