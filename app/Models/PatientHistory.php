<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientHistory extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'date',
        'fasting',
        'fasting_hours',
        'alcohol_last_24h',
        'on_medication',
        'medications',
        'on_supplements',
        'supplements',
        'chronic_disease',
        'chronic_disease_details',
        'recent_surgery',
        'surgery_details',
        'allergies',
        'allergy_details',
        'pregnant_or_lactating',
        'smokes',
        'cigarettes_per_day',
        'physically_active',
        'menstrual_period',
        'recent_fever_or_flu',
        'observation',
    ];

    protected $casts = [
        'date' => 'datetime',
        'fasting' => 'boolean',
        'alcohol_last_24h' => 'boolean',
        'on_medication' => 'boolean',
        'on_supplements' => 'boolean',
        'chronic_disease' => 'boolean',
        'recent_surgery' => 'boolean',
        'allergies' => 'boolean',
        'pregnant_or_lactating' => 'boolean',
        'smokes' => 'boolean',
        'physically_active' => 'boolean',
        'recent_fever_or_flu' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
