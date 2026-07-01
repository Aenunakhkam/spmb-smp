<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id',
    'nisn',
    'nik',
    'full_name',
    'gender',
    'place_of_birth',
    'date_of_birth',
    'religion',
    'phone',
    'email',
    'province',
    'city',
    'district',
    'village',
    'address',
    'postal_code',
    'origin_school_name',
    'origin_school_npsn',
    'origin_school_address',
    'additional_data',
])]
class StudentDetail extends Model
{
    protected $casts = [
        'additional_data' => 'array',
    ];
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
