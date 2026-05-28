<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read Patient|null $patient
 * @property-read User|null $user
 * @property-read SampleType|null $sampleType
 */
class Sample extends Model
{
    use LogsActivity;

    protected $fillable = [
        'patient_id',
        'user_id',
        'sample_type_id',
        'code',
        'date',
        'location',
        'status',
        'stored_at',
        'notified',
    ];

    protected $casts = [
        'date' => 'date',
        'stored_at' => 'date',
        'notified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function sampleType(): BelongsTo
    {
        return $this->belongsTo(SampleType::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('sample');
    }
}
