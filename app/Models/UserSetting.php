<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    protected $table = 'user_settings';

    protected $fillable = [
        'user_id',
        'email_notifications',
        'sms_notifications',
        'health_reminders',
        'appointment_reminders',
        'newsletter_subscription',
        'language',
        'timezone',
        'privacy_level',
        'share_health_data',
        'allow_ai_learning',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'health_reminders' => 'boolean',
        'appointment_reminders' => 'boolean',
        'newsletter_subscription' => 'boolean',
        'share_health_data' => 'boolean',
        'allow_ai_learning' => 'boolean',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}



