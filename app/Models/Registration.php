<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    protected $fillable = [
        'user_id',
        'registration_number',
        'access_code',
        'status',
        'step',
        'academic_year',
        'average_score',
        'rank',
        'admin_notes',
        'additional_data',
        'finalized_at',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'finalized_at' => 'datetime',
    ];

    protected $appends = ['final_score'];

    public function getFinalScoreAttribute()
    {
        $totalAchievementScore = 0;
        
        $grade = $this->grade;
        if ($grade && isset($grade->additional_data['prestasiList'])) {
            foreach ($grade->additional_data['prestasiList'] as $ach) {
                $score = $ach['score'] ?? 0;
                $totalAchievementScore += (int)$score;
            }
        }
        
        return $this->average_score + $totalAchievementScore;
    }

    public function studentDetail(): HasOne
    {
        return $this->hasOne(StudentDetail::class);
    }

    public function parentDetail(): HasOne
    {
        return $this->hasOne(ParentDetail::class);
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    protected function casts(): array
    {
        return [
            'finalized_at' => 'datetime',
        ];
    }
}
