<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $fillable = [
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'zip_code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
