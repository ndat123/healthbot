<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthTracking extends Model
{
    use HasFactory;

    protected $table = 'health_tracking';

    protected $fillable = [
        'user_id',
        'tracking_date',
        'weight',
        'height',
        'bmi',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'blood_sugar',
        'heart_rate',
        'body_temperature',
        'notes',
        'other_metrics',
    ];

    protected $casts = [
        'tracking_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'bmi' => 'decimal:2',
        'blood_pressure_systolic' => 'decimal:2',
        'blood_pressure_diastolic' => 'decimal:2',
        'blood_sugar' => 'decimal:2',
        'body_temperature' => 'decimal:2',
        'other_metrics' => 'array',
    ];

    /**
     * Get the user that owns the health tracking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate BMI based on weight and height.
     */
    public function calculateBMI(): ?float
    {
        if ($this->weight && $this->height && $this->height > 0) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }

    /**
     * Auto-calculate BMI when saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tracking) {
            if ($tracking->weight && $tracking->height) {
                $tracking->bmi = $tracking->calculateBMI();
            }
        });
    }
}

