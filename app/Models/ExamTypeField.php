<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamTypeField extends Model
{
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
}
