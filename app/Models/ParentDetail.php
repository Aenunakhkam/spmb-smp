<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id',
    'father_name',
    'father_occupation',
    'mother_name',
    'mother_occupation',
    'parent_phone',
    'parent_address',
    'aid_card_number',
    'additional_data',
])]
class ParentDetail extends Model
{
    protected $casts = [
        'additional_data' => 'array',
    ];
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
