<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read Address|null $address
 * @property int $user_id
 * @property int|null $address_id
 * @property string $birth_date
 * @property string $ethnicity
 * @property string $sex
 * @property string $cpf
 * @property string $rg
 * @property string $phone
 */
class Patient extends Model
{
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
}
