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

    /**
     * Kiểm tra xem có bật email notifications không
     */
    public function hasEmailNotifications(): bool
    {
        return $this->email_notifications ?? true;
    }

    /**
     * Kiểm tra xem có bật SMS notifications không
     */
    public function hasSMSNotifications(): bool
    {
        return $this->sms_notifications ?? false;
    }

    /**
     * Kiểm tra xem có bật health reminders không
     */
    public function hasHealthReminders(): bool
    {
        return $this->health_reminders ?? true;
    }

    /**
     * Kiểm tra xem có bật appointment reminders không
     */
    public function hasAppointmentReminders(): bool
    {
        return $this->appointment_reminders ?? true;
    }

    /**
     * Kiểm tra xem có đăng ký newsletter không
     */
    public function isSubscribedToNewsletter(): bool
    {
        return $this->newsletter_subscription ?? false;
    }

    /**
     * Kiểm tra xem có cho phép chia sẻ dữ liệu sức khỏe không
     */
    public function allowsHealthDataSharing(): bool
    {
        return $this->share_health_data ?? false;
    }

    /**
     * Kiểm tra xem có cho phép AI học tập không
     */
    public function allowsAILearning(): bool
    {
        return $this->allow_ai_learning ?? true;
    }

    /**
     * Lấy ngôn ngữ mặc định nếu chưa set
     */
    public function getLanguage(): string
    {
        return $this->language ?? 'vi';
    }

    /**
     * Lấy timezone mặc định nếu chưa set
     */
    public function getTimezone(): string
    {
        return $this->timezone ?? 'Asia/Ho_Chi_Minh';
    }

    /**
     * Lấy privacy level mặc định nếu chưa set
     */
    public function getPrivacyLevel(): string
    {
        return $this->privacy_level ?? 'private';
    }
}



