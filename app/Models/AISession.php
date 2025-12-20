<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AISession extends Model
{
    use HasFactory;

    protected $table = 'ai_sessions';

    protected $fillable = [
        'user_id',
        'session_token',
        'user_query',
        'ai_response',
        'topic',
        'emergency_level',
        'duration_seconds',
        'accuracy_score',
        'user_satisfaction',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}




