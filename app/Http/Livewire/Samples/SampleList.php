<?php

namespace App\Http\Livewire\Samples;

use App\Models\Patient;
use App\Models\Sample;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.base')]
class SampleList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $dateFilter = '';

    protected $paginationTheme = 'bootstrap';

    public function delete(Sample $sample)
    {
        $sample->delete();
        session()->flash('success', 'Amostra deletada com sucesso!');
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'dateFilter']);
    }

    public function render()
    {
        $query = Sample::with(['patient.user', 'user']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%'.$this->search.'%')
                    ->orWhere('type', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFilter) {
            $query->whereDate('date', $this->dateFilter);
        }

        $samples = $query->orderBy('date', 'desc')->paginate(10);
        $patients = Patient::with('user')->get()->sortBy('user.name');

        return view('livewire.samples.sample-list', compact('samples', 'patients'));
    }
}
