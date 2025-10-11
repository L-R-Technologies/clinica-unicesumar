<?php

namespace App\Livewire\Samples;

use App\Models\Patient;
use App\Models\Sample;
use App\Models\SampleType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

    protected function rules()
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'sample_type_id' => ['required', 'integer', 'exists:sample_types,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:under review,stored,discarded'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function mount()
    {
        $this->patients = Patient::with('user')->get()->sortBy('user.name');
        $this->sampleTypes = SampleType::orderBy('name')->get();
        $this->date = now()->format('Y-m-d');
    }

    public function save()
    {
        $validatedData = $this->validate();
        $date = Carbon::parse($this->date);

        $datePrefix = $date->format('dmY');

        $lastSample = Sample::where('code', 'like', $datePrefix.'-%')
            ->latest('code')
            ->first();

        $sequentialNumber = 1;
        if ($lastSample) {
            $parts = explode('-', $lastSample->code);
            $lastSequentialNumber = (int) end($parts);
            $sequentialNumber = $lastSequentialNumber + 1;
        }

        $code = $datePrefix.'-'.str_pad((string) $sequentialNumber, 3, '0', STR_PAD_LEFT);

        Sample::create([
            'patient_id' => $validatedData['patient_id'],
            'user_id' => Auth::id(),
            'sample_type_id' => $validatedData['sample_type_id'],
            'date' => $validatedData['date'],
            'status' => $validatedData['status'],
            'location' => $validatedData['location'],
            'code' => $code,
        ]);

        session()->flash('success', 'Amostra criada com sucesso!');

        return redirect()->route('samples.index');
    }

    public function render()
    {
        return view('livewire.samples.create-samples');
    }
}
