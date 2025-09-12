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

    public $type = '';

    public $date = '';

    public $observation = '';

    public $results = '';

    public $justification_rejection = '';

    public $patients = [];

    public $patientHistories = [];

    public $samples = [];

    public function mount(Exam $exam)
    {
        $this->exam = $exam;

        // Carrega dados do exame
        $this->patient_id = $exam->patient_id;
        $this->patient_history_id = $exam->patient_history_id;
        $this->sample_id = $exam->sample_id;
        $this->type = $exam->type;
        $this->date = $exam->date->format('Y-m-d\TH:i');
        $this->observation = $exam->observation;
        $this->results = is_array($exam->results) ? json_encode($exam->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $exam->results;
        $this->justification_rejection = $exam->justification_rejection;

        // Carrega dados para os selects
        $examService = app(ExamService::class);
        $this->patients = $examService->getPatients();
        $this->patientHistories = $examService->getPatientHistories($this->patient_id);
        $this->samples = $examService->getSamples($this->patient_id);
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

            $examData = [
                'patient_id' => $this->patient_id,
                'patient_history_id' => $this->patient_history_id,
                'sample_id' => $this->sample_id,
                'date' => $this->date,
                'observation' => $this->observation,
                'results' => $this->results,
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

    public function render()
    {
        return view('livewire.exam.edit-exam');
    }
}
