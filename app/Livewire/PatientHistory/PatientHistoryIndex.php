<?php

namespace App\Livewire\PatientHistory;

use App\Models\PatientHistory;
use App\Service\PatientHistoryService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.base')]
class PatientHistoryIndex extends Component
{
    use WithPagination;

    public string $patientFilter = '';

    public string $professionalFilter = '';

    public string $dateFilter = '';

    protected $paginationTheme = 'bootstrap';

    public function updatedPatientFilter()
    {
        $this->resetPage();
    }

    public function updatedProfessionalFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['patientFilter', 'professionalFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function delete(PatientHistory $patientHistory)
    {
        try {
            $patientHistoryService = app(PatientHistoryService::class);
            $patientHistoryService->delete($patientHistory);

            session()->flash('success', 'Anamnese removida com sucesso!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao remover anamnese: '.$e->getMessage());
        }
    }

    public function render()
    {
        $query = PatientHistory::with(['patient.user', 'user']);

        // Filtrar por responsável se for aluno
        $user = Auth::user();
        if ($user->role === 'student') {
            $query->where('user_id', $user->id);
        }
        // Professores veem todas as anamneses

        if ($this->patientFilter) {
            $query->whereHas('patient.user', function ($q) {
                $q->where('name', 'like', '%'.$this->patientFilter.'%');
            });
        }

        if ($this->professionalFilter) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->professionalFilter.'%');
            });
        }

        if ($this->dateFilter) {
            $query->whereDate('recorded_at', $this->dateFilter);
        }

        $anamneses = $query->latest('recorded_at')->paginate(10);

        return view('livewire.patient-history.patient-history-index', [
            'anamneses' => $anamneses,
        ]);
    }
}
