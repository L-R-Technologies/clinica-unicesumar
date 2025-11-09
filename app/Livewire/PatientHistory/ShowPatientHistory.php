<?php

namespace App\Livewire\PatientHistory;

use App\Models\PatientHistory;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class ShowPatientHistory extends Component
{
    public PatientHistory $patientHistory;

    public function mount(PatientHistory $patientHistory)
    {
        $this->patientHistory = $patientHistory->load(['patient.user', 'user']);
    }

    public function delete()
    {
        try {
            $this->patientHistory->delete();
            session()->flash('success', 'Anamnese removida com sucesso!');

            return redirect()->route('patient-histories.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.patient-history.show-patient-history');
    }
}
