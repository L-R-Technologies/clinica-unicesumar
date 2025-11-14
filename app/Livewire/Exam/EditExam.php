<?php

namespace App\Livewire\Exam;

use App\Models\Exam;
use App\Service\ExamService;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EditExam extends Component
{
    public $exam;

    public $patient_id = '';

    public $patient_history_id = '';

    public $sample_id = '';

    public $exam_type_id = '';

    public $date = '';

    public $observation = '';

    public $results = '';

    public $resultsData = [];

    public $patients = [];

    public $patientHistories = [];

    public $samples = [];

    public $examTypes = [];

    public function mount(Exam $exam)
    {
        $this->exam = $exam->load('examType.fields');

        $this->patient_id = $exam->patient_id;
        $this->patient_history_id = $exam->patient_history_id;
        $this->sample_id = $exam->sample_id;
        $this->exam_type_id = $exam->exam_type_id;
        $this->date = $exam->date->format('Y-m-d');
        $this->observation = $exam->observation;

        $this->initializeResultsFields();

        $examService = app(ExamService::class);
        $this->patients = $examService->getPatients();
        $this->examTypes = $examService->getExamTypes();
        $this->patientHistories = $examService->getPatientHistories($this->patient_id);
        $this->samples = $examService->getSamples($this->patient_id);
    }

    private function initializeResultsFields()
    {
        $this->results = is_array($this->exam->results) ?
            json_encode($this->exam->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) :
            ($this->exam->results ?? '');

        if ($this->exam->results) {
            $existingResults = is_array($this->exam->results) ? $this->exam->results : json_decode($this->exam->results, true);
            if (is_array($existingResults)) {
                $this->resultsData = $existingResults;
            }
        }
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
                'sample_id' => $this->sample_id ?: null,
                'exam_type_id' => $this->exam_type_id,
                'date' => $this->date,
                'observation' => $this->observation,
                'results' => $resultsToSave,
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
        if (! empty($this->resultsData) && is_array($this->resultsData)) {
            return array_filter($this->resultsData, function ($value) {
                return ! is_null($value) && $value !== '';
            });
        }

        if (! empty($this->results)) {
            $decoded = json_decode($this->results, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }
    }

    public function getResultsLabels()
    {
        if (! $this->exam || ! $this->exam->examType) {
            return [];
        }

        $fields = $this->exam->examType->fields;

        if ($fields->isEmpty()) {
            return [
                'observations' => 'Observações',
            ];
        }

        return $fields->pluck('label', 'name')->toArray();
    }

    public function getResultsFields()
    {
        if (! $this->exam || ! $this->exam->examType) {
            return collect();
        }

        return $this->exam->examType->fields;
    }

    public function render()
    {
        return view('livewire.exam.edit-exam');
    }
}
