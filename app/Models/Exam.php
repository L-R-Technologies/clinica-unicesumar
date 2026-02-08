<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read User|null $user
 * @property-read Patient|null $patient
 * @property-read PatientHistory|null $patientHistory
 * @property-read Sample|null $sample
 * @property-read ExamType|null $examType
 */
class Exam extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'patient_history_id',
        'patient_id',
        'exam_type_id',
        'sample_id',
        'date',
        'results',
        'status',
        'observation',
    ];

    protected $casts = [
        'date' => 'date',
        'results' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patientHistory(): BelongsTo
    {
        return $this->belongsTo(PatientHistory::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function rejections(): HasMany
    {
        return $this->hasMany(ExamRejection::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(ExamFeedback::class);
    }

    public function latestRejection()
    {
        return $this->hasOne(ExamRejection::class)->latestOfMany();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('exam');
    }
}
