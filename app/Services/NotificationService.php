<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Tạo thông báo trong database
     */
    public function createNotification(
        User $user,
        string $title,
        string $message,
        string $type = 'general',
        ?string $actionUrl = null,
        ?array $metadata = null
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'metadata' => $metadata,
            'is_read' => false,
        ]);
    }

    /**
     * Gửi thông báo email cho user
     */
    public function sendEmailNotification(User $user, string $subject, string $message, string $type = 'general', ?string $actionUrl = null): bool
    {
        try {
            $settings = $user->settings;
            
            // Tạo thông báo trong database
            $this->createNotification($user, $subject, $message, $type, $actionUrl);
            
            // Kiểm tra xem user có bật email notifications không
            if (!$settings || !$settings->email_notifications) {
                Log::info("Email notifications disabled for user {$user->id}");
                return false;
            }

            // Lấy ngôn ngữ từ settings
            $language = $settings->language ?? 'vi';
            
            // Gửi email
            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)
                     ->subject($subject);
            });

            Log::info("Email notification sent to user {$user->id}: {$subject}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email notification to user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi thông báo SMS cho user
     */
    public function sendSMSNotification(User $user, string $message, string $type = 'general'): bool
    {
        try {
            $settings = $user->settings;
            
            // Tạo thông báo trong database
            $this->createNotification($user, 'Thông báo SMS', $message, $type);
            
            // Kiểm tra xem user có bật SMS notifications không
            if (!$settings || !$settings->sms_notifications) {
                Log::info("SMS notifications disabled for user {$user->id}");
                return false;
            }

            // Kiểm tra xem user có số điện thoại không
            if (!$user->phone) {
                Log::warning("User {$user->id} does not have a phone number");
                return false;
            }

            // TODO: Tích hợp với SMS gateway (Twilio, Nexmo, etc.)
            // Ví dụ với Twilio:
            /*
            $accountSid = env('TWILIO_ACCOUNT_SID');
            $authToken = env('TWILIO_AUTH_TOKEN');
            $fromNumber = env('TWILIO_FROM_NUMBER');

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                    'From' => $fromNumber,
                    'To' => $user->phone,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                Log::info("SMS notification sent to user {$user->id}");
                return true;
            }
            */

            // Tạm thời log thay vì gửi thật
            Log::info("SMS notification would be sent to user {$user->id} at {$user->phone}: {$message}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send SMS notification to user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi thông báo cho nhiều user cùng lúc
     */
    public function sendBulkNotifications(array $userIds, string $subject, string $message, string $type = 'general'): array
    {
        $results = [
            'email_sent' => 0,
            'email_failed' => 0,
            'sms_sent' => 0,
            'sms_failed' => 0,
        ];

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            if ($this->sendEmailNotification($user, $subject, $message, $type)) {
                $results['email_sent']++;
            } else {
                $results['email_failed']++;
            }

            if ($this->sendSMSNotification($user, $message)) {
                $results['sms_sent']++;
            } else {
                $results['sms_failed']++;
            }
        }

        return $results;
    }

    /**
     * Kiểm tra xem user có muốn nhận thông báo loại này không
     */
    public function shouldNotify(User $user, string $notificationType): bool
    {
        $settings = $user->settings;
        
        if (!$settings) {
            return false;
        }

        switch ($notificationType) {
            case 'email':
                return $settings->email_notifications ?? false;
            case 'sms':
                return $settings->sms_notifications ?? false;
            case 'health_reminder':
                return $settings->health_reminders ?? false;
            case 'appointment_reminder':
                return $settings->appointment_reminders ?? false;
            default:
                return false;
        }
    }
}

