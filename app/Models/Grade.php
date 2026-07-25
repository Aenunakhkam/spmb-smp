<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id',
    'mathematics',
    'indonesian',
    'english',
    'religion',
    'ipa',
    'ips',
    'pkn',
    'proof_file_path',
    'additional_data',
])]
class Grade extends Model
{
    protected $casts = [
        'additional_data' => 'array',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
