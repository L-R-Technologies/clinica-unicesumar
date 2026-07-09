<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read Address|null $address
 * @property-read User|null $user
 * @property int $user_id
 * @property int|null $address_id
 * @property string $birthday
 * @property string $ethnicity
 * @property string $sex
 * @property string $cpf
 * @property string $rg
 * @property string $phone
 */
class Patient extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'address_id',
        'birthday',
        'ethnicity',
        'sex',
        'cpf',
        'rg',
        'phone',
        'lgpd_consent_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birthday' => 'date',
        'lgpd_consent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function patientHistories(): HasMany
    {
        return $this->hasMany(PatientHistory::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function samples(): HasMany
    {
        return $this->hasMany(Sample::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('patient');
    }
}
