<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return HasMany<ExamTypeField, $this>
     */
    public function fields(): HasMany
    {
        return $this->hasMany(ExamTypeField::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
