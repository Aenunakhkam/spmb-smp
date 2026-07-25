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
        
        static $achievementScores = null;
        if ($achievementScores === null) {
            $achievementScores = json_decode(\App\Models\Setting::where('key', 'achievement_scores')->first()?->value ?? '{}', true);
        }
        
        $studentDetail = $this->studentDetail;
        if ($studentDetail && isset($studentDetail->additional_data['achievements'])) {
            foreach ($studentDetail->additional_data['achievements'] as $ach) {
                $level = $ach['level'] ?? null;
                $rank = $ach['rank'] ?? null;
                if ($level && $rank && isset($achievementScores[$level][$rank])) {
                    $totalAchievementScore += $achievementScores[$level][$rank];
                }
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
