<?php

namespace App\Livewire\ActivityLog;

use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class ActivityLogIndex extends Component
{
    public $search = '';

    public $logNameFilter = '';

    public $eventFilter = '';

    public $dateFrom = '';

    public $dateTo = '';

    public function updatedSearch() {}

    public function updatedLogNameFilter() {}

    public function updatedEventFilter() {}

    public function updatedDateFrom() {}

    public function updatedDateTo() {}

    public function clearFilters()
    {
        $this->search = '';
        $this->logNameFilter = '';
        $this->eventFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
    }

    public function getLogs()
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        if (! empty($this->logNameFilter)) {
            $query->where('log_name', $this->logNameFilter);
        }

        if (! empty($this->eventFilter)) {
            $query->where('event', $this->eventFilter);
        }

        if (! empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->paginate(20);
    }

    public function getLogNames()
    {
        return Activity::select('log_name')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');
    }

    public function getEvents()
    {
        return Activity::select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');
    }

    public function render()
    {
        return view('livewire.activity-log.activity-log-index', [
            'logs' => $this->getLogs(),
            'logNames' => $this->getLogNames(),
            'events' => $this->getEvents(),
        ]);
    }
}
