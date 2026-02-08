<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read Student|null $student
 * @property-read Teacher|null $teacher
 * @property-read Patient|null $patient
 * @property string $role
 * @property string|null $remember_token
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active',
        'email_verified_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function samples(): HasMany
    {
        return $this->hasMany(Sample::class);
    }

    public function patientHistories(): HasMany
    {
        return $this->hasMany(PatientHistory::class);
    }

    public function calibrations(): HasMany
    {
        return $this->hasMany(Calibration::class);
    }

    public function examRejections(): HasMany
    {
        return $this->hasMany(ExamRejection::class);
    }

    public function supervisedTeachers(): HasMany
    {
        return $this->hasMany(Teacher::class, 'supervisor_id');
    }

    public function supervisedStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'supervisor_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'active', 'email_verified_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user');
    }

    public function anonymizePersonalData(): void
    {
        $timestamp = now()->timestamp;
        $anonymizedId = 'anonymous_'.$this->id.'_'.$timestamp;

        $this->name = 'Usuário Anonimizado';
        $this->email = $anonymizedId.'@email.com';
        $this->password = bcrypt(str()->random(60));
        $this->active = false;
        $this->email_verified_at = null;
        $this->remember_token = null;
        $this->saveQuietly();

        $this->patient->cpf = '00000000000';
        $this->patient->rg = '000000000';
        $this->patient->phone = '00000000000';
        $this->patient->birthday = '1900-01-01';
        $this->patient->ethnicity = 'Não informado';
        $this->patient->sex = 'prefer_not_say';
        $this->patient->saveQuietly();

        if ($this->patient->address) {
            $this->patient->address->street = 'Rua Anonimizada';
            $this->patient->address->number = '0';
            $this->patient->address->complement = null;
            $this->patient->address->neighborhood = 'Bairro Anonimizado';
            $this->patient->address->city = 'Cidade Anonimizada';
            $this->patient->address->state = 'XX';
            $this->patient->address->country = 'Brasil';
            $this->patient->address->zip_code = '00000-000';
            $this->patient->address->saveQuietly();
        }

        activity('user')
            ->performedOn($this)
            ->withProperties(['anonymized_at' => now()])
            ->log('Dados pessoais anonimizados (LGPD)');
    }
}
