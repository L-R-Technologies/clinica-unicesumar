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

    public $search = '';

    public $statusFilter = '';

    public $dateFilter = '';

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function updatedSearch($value)
    {
        $this->resetPage();
    }

    public function updatedStatusFilter($value)
    {
        $this->resetPage();
    }

    public function updatedDateFilter($value)
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
            session()->flash('error', $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'dateFilter']);
        $this->resetPage();
        $this->dispatch('refreshComponent');
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
            'samples' => $samples,
            'statusOptions' => $sampleService->getStatusOptions(),
        ]);
    }
}
