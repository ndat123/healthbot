<?php

/**
 * VÍ DỤ TÍCH HỢP SETTINGS VÀO CÁC CONTROLLER
 * 
 * File này minh họa cách sử dụng các services và helpers trong các controller thực tế
 */

namespace App\Examples;

use App\Models\User;
use App\Services\NotificationService;
use App\Services\ReminderService;
use App\Services\NewsletterService;
use App\Helpers\SettingsHelper;
use Carbon\Carbon;

class SettingsIntegrationExample
{
    /**
     * VÍ DỤ 1: Trong HealthPlanController - Gửi thông báo khi tạo plan mới
     */
    public static function exampleHealthPlanCreated()
    {
        /*
        // Trong HealthPlanController@store
        public function store(Request $request)
        {
            // ... tạo health plan ...
            
            $user = Auth::user();
            $notificationService = app(NotificationService::class);
            
            // Gửi thông báo email nếu user cho phép
            if ($notificationService->shouldNotify($user, 'email')) {
                $notificationService->sendEmailNotification(
                    $user,
                    'Kế hoạch sức khỏe mới đã được tạo',
                    "Kế hoạch sức khỏe {$durationDays} ngày của bạn đã sẵn sàng. Hãy xem chi tiết trong hồ sơ của bạn.",
                    'health_plan_created'
                );
            }
            
            // Tạo reminder để kiểm tra tiến độ sau 1 tuần
            $reminderService = app(ReminderService::class);
            $reminderService->createHealthReminder(
                $user,
                'Kiểm tra tiến độ kế hoạch sức khỏe',
                'Đã 1 tuần kể từ khi bắt đầu kế hoạch. Hãy kiểm tra tiến độ của bạn.',
                Carbon::now()->addWeek(),
                [],
                false
            );
            
            return redirect()->route('health-plans.index')
                ->with('success', 'Kế hoạch sức khỏe đã được tạo!');
        }
        */
    }

    /**
     * VÍ DỤ 2: Trong DoctorController - Gửi thông báo khi đặt lịch hẹn
     */
    public static function exampleAppointmentBooked()
    {
        /*
        // Trong DoctorController@store
        public function store(Request $request)
        {
            // ... tạo appointment ...
            
            $user = Auth::user();
            $notificationService = app(NotificationService::class);
            $reminderService = app(ReminderService::class);
            
            // Gửi email xác nhận
            if ($notificationService->shouldNotify($user, 'email')) {
                $notificationService->sendEmailNotification(
                    $user,
                    'Xác nhận cuộc hẹn với bác sĩ',
                    "Bạn đã đặt lịch hẹn với bác sĩ {$doctor->name} vào {$appointmentDate->format('d/m/Y H:i')}",
                    'appointment_confirmed'
                );
            }
            
            // Gửi SMS nếu user cho phép
            if ($notificationService->shouldNotify($user, 'sms')) {
                $notificationService->sendSMSNotification(
                    $user,
                    "Xác nhận: Cuộc hẹn với bác sĩ {$doctor->name} vào {$appointmentDate->format('d/m/Y H:i')}"
                );
            }
            
            // Tạo reminder 1 ngày trước cuộc hẹn
            if ($user->settings && $user->settings->appointment_reminders) {
                $reminderService->createAppointmentReminder(
                    $user,
                    "Nhắc nhở: Cuộc hẹn với bác sĩ {$doctor->name}",
                    "Bạn có cuộc hẹn với bác sĩ {$doctor->name} vào ngày mai",
                    $appointmentDate->subDay()
                );
            }
            
            return redirect()->route('doctor.appointment.show', $appointment->id)
                ->with('success', 'Đặt lịch hẹn thành công!');
        }
        */
    }

    /**
     * VÍ DỤ 3: Trong HealthJournalController - Sử dụng timezone và language
     */
    public static function exampleHealthJournalCreated()
    {
        /*
        // Trong HealthJournalController@store
        public function store(Request $request)
        {
            $user = Auth::user();
            
            // Sử dụng timezone của user
            $userTimezone = SettingsHelper::getUserTimezone($user);
            $journalDate = Carbon::parse($request->journal_date, $userTimezone);
            
            // Tạo journal entry
            $journal = HealthJournal::create([
                'user_id' => $user->id,
                'journal_date' => $journalDate->utc(), // Lưu dưới dạng UTC
                // ... other fields
            ]);
            
            // Format thời gian để hiển thị cho user
            $formattedDate = SettingsHelper::formatDateTimeForUser($journal->journal_date, $user);
            
            return view('health-journal.show', [
                'journal' => $journal,
                'formattedDate' => $formattedDate,
            ]);
        }
        */
    }

    /**
     * VÍ DỤ 4: Trong AdminController - Gửi newsletter
     */
    public static function exampleSendNewsletter()
    {
        /*
        // Trong AdminController
        public function sendNewsletter(Request $request)
        {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
            ]);
            
            $newsletterService = app(NewsletterService::class);
            
            $results = $newsletterService->sendNewsletter(
                $validated['subject'],
                $validated['content']
            );
            
            return back()->with('success', 
                "Đã gửi newsletter cho {$results['sent']} người đăng ký. " .
                "Thất bại: {$results['failed']}, Bỏ qua: {$results['skipped']}"
            );
        }
        */
    }

    /**
     * VÍ DỤ 5: Kiểm tra privacy level khi hiển thị profile
     */
    public static function exampleCheckPrivacy()
    {
        /*
        // Trong ProfileController hoặc bất kỳ controller nào
        public function showProfile(User $user)
        {
            $viewer = Auth::user();
            
            // Kiểm tra quyền truy cập
            if (!SettingsHelper::canAccess($user, $viewer)) {
                abort(403, 'Bạn không có quyền xem hồ sơ này');
            }
            
            // Chỉ hiển thị dữ liệu sức khỏe nếu privacy cho phép
            $healthData = null;
            if (SettingsHelper::canAccess($user, $viewer)) {
                $healthData = $user->healthProfile;
            }
            
            return view('profile.show', [
                'user' => $user,
                'healthData' => $healthData,
            ]);
        }
        */
    }

    /**
     * VÍ DỤ 6: Sử dụng language setting trong response
     */
    public static function exampleUseLanguage()
    {
        /*
        // Trong bất kỳ controller nào
        public function someAction(Request $request)
        {
            $user = Auth::user();
            $language = SettingsHelper::getUserLanguage($user);
            
            // Load translations theo language
            $messages = [
                'vi' => [
                    'success' => 'Thành công',
                    'error' => 'Lỗi',
                ],
                'en' => [
                    'success' => 'Success',
                    'error' => 'Error',
                ],
            ];
            
            $message = $messages[$language]['success'] ?? $messages['vi']['success'];
            
            return response()->json([
                'message' => $message,
                'language' => $language,
            ]);
        }
        */
    }
}

