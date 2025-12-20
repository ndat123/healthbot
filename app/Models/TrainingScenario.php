<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingScenario extends Model
{
    use HasFactory;

    protected $table = 'training_scenarios';

    protected $fillable = [
        'name',
        'description',
        'scenario_data',
        'status',
        'training_progress',
        'created_by',
    ];

    protected $casts = [
        'scenario_data' => 'array',
        'training_progress' => 'integer',
    ];

    /**
     * Get the user who created this scenario.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

