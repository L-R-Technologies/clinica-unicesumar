<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Machine extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name', 'model', 'serial_number', 'location',
        'calibration_range_min', 'calibration_range_max', 'status',
    ];

    protected $casts = [
        'calibration_range_min' => 'float',
        'calibration_range_max' => 'float',
    ];

    public function calibrations(): HasMany
    {
        return $this->hasMany(Calibration::class);
    }

    /**
     * @return HasMany<LabAgent, $this>
     */
    public function labAgents(): HasMany
    {
        return $this->hasMany(LabAgent::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('Máquina')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
