<?php

namespace App\Livewire\Exam;

use App\Enums\ExamType;
use App\Models\Exam;
use App\Service\ExamService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use ValueError;

class EditExam extends Component
{
    public $exam;

    public $patient_id = '';

    public $patient_history_id = '';

    public $sample_id = '';

    public $type = '';

    public $date = '';

    public $observation = '';

    public $results = '';

    public $resultsData = [];

    public $justification_rejection = '';

    public $patients = [];

    public $patientHistories = [];

    public $samples = [];

    public function mount(Exam $exam)
    {
        $this->exam = $exam;

        $this->patient_id = $exam->patient_id;
        $this->patient_history_id = $exam->patient_history_id;
        $this->sample_id = $exam->sample_id;
        $this->type = $exam->type->value; // Obtém o valor string do enum
        $this->date = $exam->date->format('Y-m-d\TH:i');
        $this->observation = $exam->observation;

        $this->initializeResultsFields();

        $this->justification_rejection = $exam->justification_rejection;

        $examService = app(ExamService::class);
        $this->patients = $examService->getPatients();
        $this->patientHistories = $examService->getPatientHistories($this->patient_id);
        $this->samples = $examService->getSamples($this->patient_id);
    }

    private function initializeResultsFields()
    {
        $examTypeEnum = $this->getExamTypeEnum();

        if ($examTypeEnum) {
            $defaultResults = $examTypeEnum->getDefaultResults();

            $existingResults = is_array($this->exam->results) ? $this->exam->results : [];

            foreach ($defaultResults as $key => $defaultValue) {
                $this->resultsData[$key] = $existingResults[$key] ?? $defaultValue;
            }
        } else {
            $this->results = is_array($this->exam->results) ?
                json_encode($this->exam->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) :
                $this->exam->results;
        }
    }

    private function getExamTypeEnum(): ?ExamType
    {
        try {
            return ExamType::from($this->type);
        } catch (ValueError $e) {
            return null;
        }
    }

    public function getResultsLabels(): array
    {
        $examTypeEnum = $this->getExamTypeEnum();

        return $examTypeEnum ? $examTypeEnum->getResultsLabels() : [];
    }

    public function updatedPatientId()
    {
        if ($this->patient_id) {
            $examService = app(ExamService::class);
            $this->patientHistories = $examService->getPatientHistories($this->patient_id);
            $this->samples = $examService->getSamples($this->patient_id);
        } else {
            $this->patientHistories = [];
            $this->samples = [];
        }
        $this->patient_history_id = '';
        $this->sample_id = '';
    }

    public function save()
    {
        try {
            $examService = app(ExamService::class);

            $resultsToSave = $this->prepareResults();

            $examData = [
                'patient_id' => $this->patient_id,
                'patient_history_id' => $this->patient_history_id,
                'sample_id' => $this->sample_id,
                'date' => $this->date,
                'observation' => $this->observation,
                'results' => $resultsToSave,
                'justification_rejection' => $this->justification_rejection,
            ];

            $validatedData = $examService->validateExamData($examData, $this->exam->id);
            $examService->updateExam($this->exam, $validatedData);

            session()->flash('success', 'Exame atualizado com sucesso!');

            return redirect()->route('exam.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao atualizar exame: '.$e->getMessage());
        }
    }

    private function prepareResults()
    {
        $examTypeEnum = $this->getExamTypeEnum();

        if ($examTypeEnum && ! empty($this->resultsData)) {
            $filteredResults = array_filter($this->resultsData, function ($value) {
                return $value !== '' && $value !== null;
            });

            return ! empty($filteredResults) ? $filteredResults : null;
        } else {
            return $this->results ?: null;
        }
    }

    public function render()
    {
        return view('livewire.exam.edit-exam');
    }
}
