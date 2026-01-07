<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserSetting;
use Carbon\Carbon;

class SettingsHelper
{
    /**
     * Lấy ngôn ngữ của user
     */
    public static function getUserLanguage(User $user): string
    {
        $settings = $user->settings;
        return $settings ? ($settings->language ?? 'vi') : 'vi';
    }

    /**
     * Lấy timezone của user
     */
    public static function getUserTimezone(User $user): string
    {
        $settings = $user->settings;
        return $settings ? ($settings->timezone ?? 'Asia/Ho_Chi_Minh') : 'Asia/Ho_Chi_Minh';
    }

    /**
     * Chuyển đổi thời gian theo timezone của user
     */
    public static function convertToUserTimezone(Carbon $dateTime, User $user): Carbon
    {
        $timezone = self::getUserTimezone($user);
        return $dateTime->setTimezone($timezone);
    }

    /**
     * Chuyển đổi thời gian từ timezone của user sang UTC
     */
    public static function convertFromUserTimezone(Carbon $dateTime, User $user): Carbon
    {
        $timezone = self::getUserTimezone($user);
        return $dateTime->setTimezone('UTC');
    }

    /**
     * Kiểm tra quyền truy cập dựa trên privacy level
     */
    public static function canAccess(User $owner, ?User $viewer = null): bool
    {
        if (!$viewer) {
            return false;
        }

        // User luôn có thể xem chính mình
        if ($owner->id === $viewer->id) {
            return true;
        }

        $settings = $owner->settings;
        if (!$settings) {
            return false;
        }

        $privacyLevel = $settings->privacy_level ?? 'private';

        switch ($privacyLevel) {
            case 'public':
                return true;
            case 'friends':
                // TODO: Implement friend system
                // Tạm thời return false
                return false;
            case 'private':
            default:
                return false;
        }
    }

    /**
     * Kiểm tra xem có thể chia sẻ dữ liệu sức khỏe cho nghiên cứu không
     */
    public static function canShareHealthData(User $user): bool
    {
        $settings = $user->settings;
        return $settings && ($settings->share_health_data ?? false);
    }

    /**
     * Kiểm tra xem có cho phép AI học tập không
     */
    public static function allowAILearning(User $user): bool
    {
        $settings = $user->settings;
        return $settings && ($settings->allow_ai_learning ?? true);
    }

    /**
     * Lấy tất cả settings của user dưới dạng array
     */
    public static function getUserSettingsArray(User $user): array
    {
        $settings = $user->settings;
        
        if (!$settings) {
            return [
                'email_notifications' => true,
                'sms_notifications' => false,
                'health_reminders' => true,
                'appointment_reminders' => true,
                'newsletter_subscription' => false,
                'language' => 'vi',
                'timezone' => 'Asia/Ho_Chi_Minh',
                'privacy_level' => 'private',
                'share_health_data' => false,
                'allow_ai_learning' => true,
            ];
        }

        return [
            'email_notifications' => $settings->email_notifications ?? true,
            'sms_notifications' => $settings->sms_notifications ?? false,
            'health_reminders' => $settings->health_reminders ?? true,
            'appointment_reminders' => $settings->appointment_reminders ?? true,
            'newsletter_subscription' => $settings->newsletter_subscription ?? false,
            'language' => $settings->language ?? 'vi',
            'timezone' => $settings->timezone ?? 'Asia/Ho_Chi_Minh',
            'privacy_level' => $settings->privacy_level ?? 'private',
            'share_health_data' => $settings->share_health_data ?? false,
            'allow_ai_learning' => $settings->allow_ai_learning ?? true,
        ];
    }

    /**
     * Format thời gian theo timezone và ngôn ngữ của user
     */
    public static function formatDateTimeForUser(Carbon $dateTime, User $user, string $format = 'd/m/Y H:i'): string
    {
        $timezone = self::getUserTimezone($user);
        $language = self::getUserLanguage($user);

        // Set locale nếu cần
        if ($language === 'vi') {
            Carbon::setLocale('vi');
        }

        return $dateTime->setTimezone($timezone)->format($format);
    }
}

