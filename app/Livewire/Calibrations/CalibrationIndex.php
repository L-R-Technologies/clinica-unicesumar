<?php

namespace App\Livewire\Calibrations;

use App\Models\Calibration;
use App\Models\Machine;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.base')]
class CalibrationIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $machineFilter = '';

    public function export()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=calibracoes_'.now()->format('d_m_Y').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $query = Calibration::with(['machine', 'user'])
            ->when($this->search, function ($q) {
                $q->whereHas('machine', fn ($m) => $m->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->machineFilter, fn ($q) => $q->where('machine_id', $this->machineFilter));

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Equipamento', 'Série', 'Data', 'Valor', 'Status', 'Responsável']);

            foreach ($query->get() as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->machine->name,
                    $row->machine->serial_number,
                    $row->calibration_date->format('d/m/Y'),
                    $row->value,
                    $row->status === 'approved' ? 'Aprovada' : 'Rejeitada',
                    $row->user->name,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function render()
    {
        $query = Calibration::with(['machine', 'user'])
            ->when($this->search, function ($q) {
                $q->whereHas('machine', fn ($m) => $m->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->machineFilter, fn ($q) => $q->where('machine_id', $this->machineFilter));

        return view('livewire.calibrations.calibration-index', [
            'calibrations' => $query->latest('calibration_date')->paginate(10),
            'machines' => Machine::active()->get(),
        ]);
    }
}
