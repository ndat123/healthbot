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
        'ai_prompt_used',
        'ai_response',
    ];

    protected $casts = [
        'plan_data' => 'array',
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
}

