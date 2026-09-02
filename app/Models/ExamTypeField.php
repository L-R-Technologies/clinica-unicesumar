<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $name
 * @property string $field_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ExamTypeFieldReference> $references
 */
class ExamTypeField extends Model
{
    use LogsActivity;

    protected $fillable = [
        'exam_type_id',
        'name',
        'label',
        'field_type',
        'unit',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    /**
     * @return HasMany<ExamTypeFieldReference, $this>
     */
    public function references(): HasMany
    {
        return $this->hasMany(ExamTypeFieldReference::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('exam_type_field');
    }
}
