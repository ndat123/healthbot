<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HealthJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'journal_date',
        'symptoms',
        'food_diary',
        'exercise_log',
        'mood',
        'mood_score',
        'mood_notes',
        'ai_suggestions',
        'ai_warnings',
        'risk_level',
        'doctor_recommended',
        'doctor_recommendation_reason',
        'notes',
    ];

    protected $casts = [
        'journal_date' => 'date',
        'ai_suggestions' => 'array',
        'ai_warnings' => 'array',
        'doctor_recommended' => 'boolean',
    ];

    /**
     * Get the user that owns the health journal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get mood label
     */
    public function getMoodLabelAttribute(): string
    {
        return match($this->mood) {
            'excellent' => 'Excellent',
            'good' => 'Good',
            'okay' => 'Okay',
            'poor' => 'Poor',
            'very_poor' => 'Very Poor',
            default => 'Not Set'
        };
    }

    /**
     * Get risk level color
     */
    public function getRiskLevelColorAttribute(): string
    {
        return match($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }
}

