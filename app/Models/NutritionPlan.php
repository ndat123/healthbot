<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class NutritionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'health_profile_id',
        'title',
        'plan_data',
        'duration_days',
        'start_date',
        'end_date',
        'status',
        'dietary_preferences',
        'allergies_restrictions',
        'daily_calories',
        'progress_data',
        'completion_percentage',
        'ai_prompt_used',
        'ai_response',
    ];

    protected $casts = [
        'plan_data' => 'array',
        'progress_data' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_calories' => 'decimal:2',
    ];

    /**
     * Get the user that owns the nutrition plan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the health profile associated with this plan.
     */
    public function healthProfile(): BelongsTo
    {
        return $this->belongsTo(HealthProfile::class);
    }

    /**
     * Calculate completion percentage based on progress.
     */
    public function updateCompletionPercentage(): void
    {
        if (!$this->progress_data || !$this->plan_data) {
            $this->completion_percentage = 0;
            return;
        }

        $totalDays = $this->duration_days;
        $completedDays = 0;

        if (isset($this->progress_data['daily_progress'])) {
            $completedDays = count(array_filter($this->progress_data['daily_progress'], function($day) {
                return isset($day['completed']) && $day['completed'] === true;
            }));
        }

        $this->completion_percentage = $totalDays > 0 
            ? round(($completedDays / $totalDays) * 100) 
            : 0;
    }
}

