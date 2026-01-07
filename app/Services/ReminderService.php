<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use App\Models\Reminder;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tạo nhắc nhở sức khỏe cho user
     */
    public function createHealthReminder(User $user, string $title, string $description, Carbon $reminderTime, array $reminderDays = [], bool $isRecurring = false): ?Reminder
    {
        try {
            $settings = $user->settings;
            
            // Kiểm tra xem user có bật health reminders không
            if (!$settings || !$settings->health_reminders) {
                Log::info("Health reminders disabled for user {$user->id}");
                return null;
            }

            $reminder = Reminder::create([
                'user_id' => $user->id,
                'reminder_type' => 'health_checkup',
                'title' => $title,
                'description' => $description,
                'reminder_time' => $reminderTime->format('H:i'),
                'reminder_days' => $reminderDays,
                'is_active' => true,
                'is_recurring' => $isRecurring,
                'start_date' => $reminderTime->copy()->startOfDay(),
            ]);

            Log::info("Health reminder created for user {$user->id}: {$title}");
            return $reminder;
        } catch (\Exception $e) {
            Log::error("Failed to create health reminder for user {$user->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo nhắc nhở cuộc hẹn cho user
     */
    public function createAppointmentReminder(User $user, string $title, string $description, Carbon $appointmentDate): ?Reminder
    {
        try {
            $settings = $user->settings;
            
            // Kiểm tra xem user có bật appointment reminders không
            if (!$settings || !$settings->appointment_reminders) {
                Log::info("Appointment reminders disabled for user {$user->id}");
                return null;
            }

            $reminder = Reminder::create([
                'user_id' => $user->id,
                'reminder_type' => 'appointment',
                'title' => $title,
                'description' => $description,
                'reminder_time' => $appointmentDate->format('H:i'),
                'is_active' => true,
                'is_recurring' => false,
                'start_date' => $appointmentDate->copy()->startOfDay(),
            ]);

            Log::info("Appointment reminder created for user {$user->id}: {$title}");
            return $reminder;
        } catch (\Exception $e) {
            Log::error("Failed to create appointment reminder for user {$user->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Gửi nhắc nhở sắp tới (chạy qua cron job)
     */
    public function sendUpcomingReminders(): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
        ];

        // Lấy các reminder active và nên trigger hôm nay
        $reminders = Reminder::where('is_active', true)
            ->with('user')
            ->get();

        foreach ($reminders as $reminder) {
            if (!$reminder->shouldTriggerToday()) {
                continue;
            }

            $user = $reminder->user;
            if (!$user) {
                continue;
            }

            $settings = $user->settings;
            if (!$settings) {
                continue;
            }

            // Kiểm tra loại reminder
            $shouldNotify = false;
            $notificationType = '';

            if ($reminder->reminder_type === 'health_checkup' && $settings->health_reminders) {
                $shouldNotify = true;
                $notificationType = 'health_reminder';
            } elseif ($reminder->reminder_type === 'appointment' && $settings->appointment_reminders) {
                $shouldNotify = true;
                $notificationType = 'appointment_reminder';
            }

            if ($shouldNotify) {
                $reminderTime = Carbon::parse($reminder->reminder_time);
                $reminderDateTime = Carbon::today()->setTimeFromTimeString($reminder->reminder_time);

                $subject = "Nhắc nhở: {$reminder->title}";
                $message = "Bạn có một nhắc nhở: {$reminder->title}";
                if ($reminder->description) {
                    $message .= "\n{$reminder->description}";
                }
                $message .= "\nThời gian: " . $reminderDateTime->format('d/m/Y H:i');

                // Tạo notification trong database
                $notificationType = $reminder->reminder_type === 'appointment' ? 'appointment' : 'reminder';
                $this->notificationService->createNotification(
                    $user,
                    $subject,
                    $message,
                    $notificationType,
                    null, // action_url - có thể thêm route đến reminders page
                    ['reminder_id' => $reminder->id]
                );

                // Gửi email
                if ($settings->email_notifications) {
                    $emailMessage = "Xin chào {$user->name},\n\n";
                    $emailMessage .= $message . "\n\n";
                    $emailMessage .= "Trân trọng,\nAI HealthBot";
                    $this->notificationService->sendEmailNotification($user, $subject, $emailMessage, $notificationType);
                }

                // Gửi SMS
                if ($settings->sms_notifications) {
                    $this->notificationService->sendSMSNotification($user, "Nhắc nhở: {$reminder->title} - {$reminderDateTime->format('d/m/Y H:i')}", $notificationType);
                }

                // Đánh dấu đã trigger
                $reminder->update(['last_triggered_at' => now()]);
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Lấy tất cả reminders của user
     */
    public function getUserReminders(User $user, bool $upcomingOnly = false): \Illuminate\Database\Eloquent\Collection
    {
        $query = Reminder::where('user_id', $user->id);

        if ($upcomingOnly) {
            $query->where('is_active', true)
                  ->where(function($q) {
                      $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                  });
        }

        return $query->orderBy('start_date', 'asc')->get();
    }

    /**
     * Tắt reminder (thay vì xóa)
     */
    public function deactivateReminder(Reminder $reminder): bool
    {
        try {
            $reminder->update(['is_active' => false]);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to deactivate reminder: " . $e->getMessage());
            return false;
        }
    }
}

