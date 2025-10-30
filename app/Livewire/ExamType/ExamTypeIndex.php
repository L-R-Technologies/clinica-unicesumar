<?php

namespace App\Livewire\ExamType;

use App\Models\ExamType;
use App\Service\ExamTypeService;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class ExamTypeIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'bootstrap';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function deleteExamType($id)
    {
        try {
            $examType = ExamType::findOrFail($id);

            $examTypeService = app(ExamTypeService::class);
            $examTypeService->deleteExamType($examType);

            session()->flash('success', 'Tipo de exame removido com sucesso!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao remover tipo de exame: '.$e->getMessage());
        }
    }

    public function getExamTypes()
    {
        $examTypeService = app(ExamTypeService::class);

        return $examTypeService->getFilteredExamTypes([
            'search' => $this->search,
        ]);
    }

    public function render()
    {
        return view('livewire.exam-type.index-exam-type', [
            'examTypes' => $this->getExamTypes(),
        ]);
    }
}
