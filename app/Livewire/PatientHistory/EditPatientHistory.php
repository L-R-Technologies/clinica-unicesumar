<?php

namespace App\Livewire\PatientHistory;

use App\Models\PatientHistory;
use App\Service\PatientHistoryService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class EditPatientHistory extends Component
{
    public PatientHistory $patientHistory;

    public ?int $patient_id = null;

    public string $recorded_at = '';

    public ?bool $fasting = null;

    public ?int $fasting_hours = null;

    public ?bool $alcohol_last_24h = null;

    public ?bool $on_medication = null;

    public string $medications = '';

    public ?bool $on_supplements = null;

    public string $supplements = '';

    public ?bool $chronic_disease = null;

    public string $chronic_disease_details = '';

    public ?bool $infectious_disease_history = null;

    public string $infectious_disease_details = '';

    public ?bool $recent_surgery = null;

    public string $surgery_details = '';

    public ?bool $allergies = null;

    public string $allergy_details = '';

    public ?bool $pregnant_or_lactating = null;

    public string $menstrual_period = '';

    public ?bool $smokes = null;

    public ?int $cigarettes_per_day = null;

    public ?bool $physically_active = null;

    public ?bool $recent_fever_or_flu = null;

    public string $observation = '';

    public function mount(PatientHistory $patientHistory)
    {
        $this->patientHistory = $patientHistory;

        $this->patient_id = $patientHistory->patient_id;
        $this->recorded_at = $patientHistory->recorded_at->format('Y-m-d');
        $this->fasting = $patientHistory->fasting;
        $this->fasting_hours = $patientHistory->fasting_hours;
        $this->alcohol_last_24h = $patientHistory->alcohol_last_24h;
        $this->on_medication = $patientHistory->on_medication;
        $this->medications = $patientHistory->medications ?? '';
        $this->on_supplements = $patientHistory->on_supplements;
        $this->supplements = $patientHistory->supplements ?? '';
        $this->chronic_disease = $patientHistory->chronic_disease;
        $this->chronic_disease_details = $patientHistory->chronic_disease_details ?? '';
        $this->infectious_disease_history = $patientHistory->infectious_disease_history;
        $this->infectious_disease_details = $patientHistory->infectious_disease_details ?? '';
        $this->recent_surgery = $patientHistory->recent_surgery;
        $this->surgery_details = $patientHistory->surgery_details ?? '';
        $this->allergies = $patientHistory->allergies;
        $this->allergy_details = $patientHistory->allergy_details ?? '';
        $this->pregnant_or_lactating = $patientHistory->pregnant_or_lactating;
        $this->menstrual_period = $patientHistory->menstrual_period ?? '';
        $this->smokes = $patientHistory->smokes;
        $this->cigarettes_per_day = $patientHistory->cigarettes_per_day;
        $this->physically_active = $patientHistory->physically_active;
        $this->recent_fever_or_flu = $patientHistory->recent_fever_or_flu;
        $this->observation = $patientHistory->observation ?? '';
    }

    public function updatedFasting($value)
    {
        if (! $value) {
            $this->fasting_hours = null;
        }
    }

    public function updatedOnMedication($value)
    {
        if (! $value) {
            $this->medications = '';
        }
    }

    public function updatedOnSupplements($value)
    {
        if (! $value) {
            $this->supplements = '';
        }
    }

    public function updatedChronicDisease($value)
    {
        if (! $value) {
            $this->chronic_disease_details = '';
        }
    }

    public function updatedInfectiousDiseaseHistory($value)
    {
        if (! $value) {
            $this->infectious_disease_details = '';
        }
    }

    public function updatedRecentSurgery($value)
    {
        if (! $value) {
            $this->surgery_details = '';
        }
    }

    public function updatedAllergies($value)
    {
        if (! $value) {
            $this->allergy_details = '';
        }
    }

    public function updatedSmokes($value)
    {
        if (! $value) {
            $this->cigarettes_per_day = null;
        }
    }

    public function update()
    {
        try {
            $patientHistoryService = app(PatientHistoryService::class);

            $data = [
                'patient_id' => $this->patientHistory->patient_id,
                'user_id' => $this->patientHistory->user_id,
                'recorded_at' => $this->recorded_at,
                'fasting' => $this->fasting,
                'fasting_hours' => $this->fasting_hours,
                'alcohol_last_24h' => $this->alcohol_last_24h,
                'on_medication' => $this->on_medication,
                'medications' => $this->medications,
                'on_supplements' => $this->on_supplements,
                'supplements' => $this->supplements,
                'chronic_disease' => $this->chronic_disease,
                'chronic_disease_details' => $this->chronic_disease_details,
                'infectious_disease_history' => $this->infectious_disease_history,
                'infectious_disease_details' => $this->infectious_disease_details,
                'recent_surgery' => $this->recent_surgery,
                'surgery_details' => $this->surgery_details,
                'allergies' => $this->allergies,
                'allergy_details' => $this->allergy_details,
                'pregnant_or_lactating' => $this->pregnant_or_lactating,
                'menstrual_period' => $this->menstrual_period,
                'smokes' => $this->smokes,
                'cigarettes_per_day' => $this->cigarettes_per_day,
                'physically_active' => $this->physically_active,
                'recent_fever_or_flu' => $this->recent_fever_or_flu,
                'observation' => $this->observation,
            ];

            $validatedData = $patientHistoryService->validateData($data);
            $patientHistoryService->update($this->patientHistory, $validatedData);

            session()->flash('success', 'Anamnese atualizada com sucesso!');

            return redirect()->route('patient-histories.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao atualizar anamnese: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.patient-history.edit-patient-history');
    }
}
