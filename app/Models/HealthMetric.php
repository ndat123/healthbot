<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recorded_date',
        'weight',
        'height',
        'bmi',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'blood_sugar',
        'heart_rate',
        'notes',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'bmi' => 'decimal:2',
        'blood_pressure_systolic' => 'decimal:2',
        'blood_pressure_diastolic' => 'decimal:2',
        'blood_sugar' => 'decimal:2',
        'heart_rate' => 'decimal:2',
    ];

    /**
     * Get the user that owns the health metric.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate BMI automatically when weight or height changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($metric) {
            if ($metric->weight && $metric->height && $metric->height > 0) {
                $heightInMeters = $metric->height / 100;
                $metric->bmi = round($metric->weight / ($heightInMeters * $heightInMeters), 2);
            }
        });
    }
}

