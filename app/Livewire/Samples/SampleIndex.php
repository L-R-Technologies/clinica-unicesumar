<?php

namespace App\Livewire\Samples;

use App\Models\Sample; // Modelo de Amostras
use App\Service\SampleService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.base')]
class SampleIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $dateFilter = '';

    protected $paginationTheme = 'bootstrap';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    public function delete(Sample $sample)
    {
        try {
            $sampleService = app(SampleService::class);
            $sampleService->deleteSample($sample);

            session()->flash('success', 'Amostra deletada com sucesso!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao deletar amostra: '.$e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $sampleService = app(SampleService::class);

        $samples = $sampleService->getFilteredSamples([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'date' => $this->dateFilter,
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role,
        ]);

        return view('livewire.samples.sample-index', [
            'samples' => $samples, // Agora são AMOSTRAS, não tipos
            'statusOptions' => $sampleService->getStatusOptions(),
        ]);
    }
}
