<?php

namespace App\Livewire\Exam;

use App\Models\Exam;
use App\Service\ExamService;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class ExamIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $typeFilter = '';

    public $dateFrom = '';

    public $dateTo = '';

    protected $paginationTheme = 'bootstrap';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function deleteExam($examId)
    {
        try {
            $exam = Exam::findOrFail($examId);

            $examService = app(ExamService::class);
            $examService->deleteExam($exam);

            session()->flash('success', 'Exame removido com sucesso!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao remover exame: '.$e->getMessage());
        }
    }

    public function getExams()
    {
        $examService = app(ExamService::class);

        return $examService->getFilteredExams([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'exam_type_id' => $this->typeFilter,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ]);
    }

    public function render()
    {
        $examService = app(ExamService::class);

        return view('livewire.exam.exam-index', [
            'exams' => $this->getExams(),
            'statusOptions' => $examService->getStatusOptions(),
            'examTypes' => $examService->getExamTypes(),
        ]);
    }
}
