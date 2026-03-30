<?php

namespace App\Livewire\SampleType;

use App\Models\SampleType;
use App\Service\SampleTypeService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SampleTypeIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $isTeacher = false;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->isTeacher = Auth::check() && Auth::user()->hasRole('teacher');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function deleteSampleType($id)
    {
        if (! $this->isTeacher) {
            session()->flash('error', 'Você não tem permissão para desativar.');

            return;
        }

        try {
            $sampleType = SampleType::findOrFail($id);
            $sampleTypeService = app(SampleTypeService::class);
            $sampleTypeService->deleteSampleType($sampleType);

            $status = $sampleType->fresh()->is_active ? 'ativado' : 'desativado';
            session()->flash('success', "Tipo de amostra {$status} com sucesso!");
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao desativar tipo de amostra: '.$e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        if (! $this->isTeacher) {
            session()->flash('error', 'Você não tem permissão para alterar o status.');

            return;
        }

        try {
            $sampleType = SampleType::findOrFail($id);
            $sampleTypeService = app(SampleTypeService::class);
            $sampleTypeService->toggleStatus($sampleType);

            $status = $sampleType->fresh()->is_active ? 'ativado' : 'desativado';
            session()->flash('success', "Tipo de amostra {$status} com sucesso!");
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao alterar status: '.$e->getMessage());
        }
    }

    public function getSampleTypes(SampleTypeService $sampleTypeService)
    {
        return $sampleTypeService->getFilteredSampleTypes([
            'search' => $this->search,
        ]);
    }

    public function render(SampleTypeService $sampleTypeService)
    {
        return view('livewire.sample-type.sample-type-index', [
            'sampleTypes' => $this->getSampleTypes($sampleTypeService),
        ]);
    }
}
