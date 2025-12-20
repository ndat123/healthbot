<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HealthPlan extends Model
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
    ];

    /**
     * Get the user that owns the health plan.
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

    /**
     * Check if plan is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date < Carbon::today();
    }

    /**
     * Get days remaining.
     */
    public function getDaysRemaining(): int
    {
        $today = Carbon::today();
        if ($this->end_date < $today) {
            return 0;
        }
        return $today->diffInDays($this->end_date);
    }
}

