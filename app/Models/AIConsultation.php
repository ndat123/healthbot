<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIConsultation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ai_consultations';

    protected $fillable = [
        'user_id',
        'health_profile_id',
        'session_id',
        'topic',
        'consultation_type',
        'user_message',
        'ai_response',
        'emergency_level',
        'context_data',
        'suggested_specialists',
        'disclaimer_acknowledged',
        'message_count',
        'duration_seconds',
    ];

    protected $casts = [
        'context_data' => 'array',
        'suggested_specialists' => 'array',
        'disclaimer_acknowledged' => 'boolean',
    ];

    /**
     * Get the user that owns the consultation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the health profile associated with this consultation.
     */
    public function healthProfile(): BelongsTo
    {
        return $this->belongsTo(HealthProfile::class);
    }

    /**
     * Generate a unique session ID.
     */
    public static function generateSessionId(): string
    {
        return 'session_' . uniqid() . '_' . time();
    }
}

