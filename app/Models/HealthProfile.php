<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'gender',
        'height',
        'weight',
        'bmi',
        'medical_history',
        'allergies',
        'lifestyle_habits',
        'health_goals',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'blood_sugar',
        'other_metrics',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'bmi' => 'decimal:2',
        'lifestyle_habits' => 'array',
        'health_goals' => 'array',
        'other_metrics' => 'array',
        'blood_pressure_systolic' => 'decimal:2',
        'blood_pressure_diastolic' => 'decimal:2',
        'blood_sugar' => 'decimal:2',
    ];

    /**
     * Get the user that owns the health profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the health plans for this profile.
     */
    public function healthPlans(): HasMany
    {
        return $this->hasMany(HealthPlan::class);
    }

    /**
     * Calculate BMI based on height and weight.
     */
    public function calculateBMI(): ?float
    {
        if ($this->height && $this->weight && $this->height > 0) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }

    /**
     * Update BMI automatically when height or weight changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($profile) {
            if ($profile->height && $profile->weight) {
                $profile->bmi = $profile->calculateBMI();
            }
        });
    }
}

