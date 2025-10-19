<?php

namespace App\Livewire\PatientHistory;

use App\Models\Patient;
use App\Service\PatientHistoryService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class CreatePatientHistory extends Component
{
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

    public $patients = [];

    public function mount()
    {
        $this->patients = Patient::with('user')->get()->sortBy('user.name');
        $this->recorded_at = now()->format('Y-m-d\TH:i');
    }

    public function save()
    {
        try {
            $patientHistoryService = app(PatientHistoryService::class);

            $data = [
                'patient_id' => $this->patient_id,
                'user_id' => Auth::id(),
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
            $patientHistoryService->create($validatedData);

            session()->flash('success', 'Anamnese criada com sucesso!');

            return redirect()->route('patient-histories.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao criar anamnese: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.patient-history.create-patient-history');
    }
}
