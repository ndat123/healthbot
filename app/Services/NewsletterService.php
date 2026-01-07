<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Gửi newsletter cho tất cả subscribers
     */
    public function sendNewsletter(string $subject, string $content): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        // Lấy tất cả users đã đăng ký newsletter
        $subscribers = User::whereHas('settings', function ($query) {
            $query->where('newsletter_subscription', true);
        })->get();

        foreach ($subscribers as $user) {
            try {
                $settings = $user->settings;
                
                // Kiểm tra lại (double check)
                if (!$settings || !$settings->newsletter_subscription) {
                    $results['skipped']++;
                    continue;
                }

                // Gửi email newsletter
                if ($settings->email_notifications) {
                    $this->notificationService->sendEmailNotification(
                        $user,
                        $subject,
                        $content,
                        'newsletter'
                    );
                    $results['sent']++;
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send newsletter to user {$user->id}: " . $e->getMessage());
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Đăng ký newsletter
     */
    public function subscribe(User $user): bool
    {
        try {
            $settings = $user->settings;
            
            if (!$settings) {
                $settings = \App\Models\UserSetting::create([
                    'user_id' => $user->id,
                    'newsletter_subscription' => true,
                ]);
            } else {
                $settings->update(['newsletter_subscription' => true]);
            }

            Log::info("User {$user->id} subscribed to newsletter");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to subscribe user {$user->id} to newsletter: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hủy đăng ký newsletter
     */
    public function unsubscribe(User $user): bool
    {
        try {
            $settings = $user->settings;
            
            if ($settings) {
                $settings->update(['newsletter_subscription' => false]);
            }

            Log::info("User {$user->id} unsubscribed from newsletter");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to unsubscribe user {$user->id} from newsletter: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra xem user có đăng ký newsletter không
     */
    public function isSubscribed(User $user): bool
    {
        $settings = $user->settings;
        return $settings && ($settings->newsletter_subscription ?? false);
    }

    /**
     * Lấy số lượng subscribers
     */
    public function getSubscriberCount(): int
    {
        return User::whereHas('settings', function ($query) {
            $query->where('newsletter_subscription', true);
        })->count();
    }
}

