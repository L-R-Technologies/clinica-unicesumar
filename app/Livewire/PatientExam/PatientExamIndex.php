<?php

namespace App\Livewire\PatientExam;

use App\Service\ExamService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PatientExamIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $paginationTheme = 'bootstrap';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedTypeFilter() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function getExams()
    {
        $examService = app(ExamService::class);
        $patient = Auth::user()->patient;

        return $examService->getFilteredExams([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'exam_type_id' => $this->typeFilter,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'patient_id' => $patient?->id ?? 0, // 0 garante lista vazia se não houver perfil
        ]);
    }

    public function render()
    {
        $examService = app(ExamService::class);

        return view('livewire.patient-exam.patient-exam-index', [
            'exams' => $this->getExams(),
            'statusOptions' => $examService->getStatusOptions(),
            'examTypes' => $examService->getExamTypes(),
        ]);
    }
}
