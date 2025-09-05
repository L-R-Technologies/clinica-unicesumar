<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    protected $fillable = [
        'user_id',
        'patient_history_id',
        'patient_id',
        'sample_id',
        'type',
        'date',
        'results',
        'status',
        'observation',
        'justification_rejection',
    ];

    protected $casts = [
        'date' => 'datetime',
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
}
