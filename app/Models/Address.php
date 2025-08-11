<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}
