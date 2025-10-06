<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Machine extends Model
{
    protected $fillable = [
        'name',
        'model',
        'serial_number',
        'location',
        'calibration_range_min',
        'calibration_range_max',
        'status',
    ];

    protected $casts = [
        'calibration_range_min' => 'float',
        'calibration_range_max' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function calibrations(): HasMany
    {
        return $this->hasMany(Calibration::class);
    }
}
