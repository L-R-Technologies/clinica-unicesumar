<?php

namespace App\Livewire\Exam;

use App\Service\ExamService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CreateExam extends Component
{
    public $patient_id = '';

    public $patient_history_id = '';

    public $sample_id = '';

    public $exam_type_id = '';

    public $date = '';

    public $observation = '';

    public $patients = [];

    public $patientHistories = [];

    public $samples = [];

    public $examTypes = [];

    public function mount()
    {
        $examService = app(ExamService::class);
        $this->patients = $examService->getPatients();
        $this->examTypes = $examService->getExamTypes();
        $this->date = now()->format('Y-m-d\TH:i');
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
                'sample_id' => $this->sample_id ?: null,
                'exam_type_id' => $this->exam_type_id,
                'date' => $this->date,
                'observation' => $this->observation,
            ];

            $validatedData = $examService->validateExamData($examData);
            $examService->createExam($validatedData, Auth::id());

            session()->flash('success', 'Exame criado com sucesso!');

            return redirect()->route('exam.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (Exception $e) {
            $this->addError('form', 'Erro ao criar exame: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.exam.create-exam');
    }
}
