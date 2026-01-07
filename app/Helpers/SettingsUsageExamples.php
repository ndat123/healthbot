<?php

/**
 * File ví dụ về cách sử dụng các Services và Helpers cho Settings
 * 
 * Đây là file documentation, không phải file thực thi
 */

namespace App\Helpers;

use App\Models\User;
use App\Services\NotificationService;
use App\Services\ReminderService;
use App\Services\NewsletterService;
use App\Helpers\SettingsHelper;
use Carbon\Carbon;

class SettingsUsageExamples
{
    /**
     * VÍ DỤ 1: Gửi email notification cho user
     */
    public static function exampleSendEmail()
    {
        $user = User::find(1);
        $notificationService = new NotificationService();
        
        // Kiểm tra xem user có muốn nhận email không
        if ($notificationService->shouldNotify($user, 'email')) {
            $notificationService->sendEmailNotification(
                $user,
                'Thông báo sức khỏe',
                'Bạn có một thông báo mới về sức khỏe của bạn.',
                'health_update'
            );
        }
    }

    /**
     * VÍ DỤ 2: Gửi SMS notification
     */
    public static function exampleSendSMS()
    {
        $user = User::find(1);
        $notificationService = new NotificationService();
        
        if ($notificationService->shouldNotify($user, 'sms')) {
            $notificationService->sendSMSNotification(
                $user,
                'Nhắc nhở: Bạn có cuộc hẹn với bác sĩ vào ngày mai lúc 10:00'
            );
        }
    }

    /**
     * VÍ DỤ 3: Tạo health reminder
     */
    public static function exampleCreateHealthReminder()
    {
        $user = User::find(1);
        $reminderService = app(ReminderService::class);
        
        $reminder = $reminderService->createHealthReminder(
            $user,
            'Kiểm tra sức khỏe định kỳ',
            'Nhớ đi kiểm tra sức khỏe định kỳ 6 tháng một lần',
            Carbon::now()->addMonths(6),
            [1, 3, 5], // Thứ 2, 4, 6
            true // Recurring
        );
    }

    /**
     * VÍ DỤ 4: Tạo appointment reminder
     */
    public static function exampleCreateAppointmentReminder()
    {
        $user = User::find(1);
        $reminderService = app(ReminderService::class);
        
        $reminder = $reminderService->createAppointmentReminder(
            $user,
            'Cuộc hẹn với bác sĩ',
            'Cuộc hẹn với bác sĩ tim mạch',
            Carbon::now()->addDays(7)
        );
    }

    /**
     * VÍ DỤ 5: Gửi newsletter cho tất cả subscribers
     */
    public static function exampleSendNewsletter()
    {
        $newsletterService = app(NewsletterService::class);
        
        $results = $newsletterService->sendNewsletter(
            'Bản tin sức khỏe tháng này',
            'Nội dung bản tin về các cập nhật sức khỏe mới nhất...'
        );
        
        // $results sẽ chứa: ['sent' => 10, 'failed' => 2, 'skipped' => 5]
    }

    /**
     * VÍ DỤ 6: Sử dụng language và timezone settings
     */
    public static function exampleUseLanguageAndTimezone()
    {
        $user = User::find(1);
        
        // Lấy ngôn ngữ của user
        $language = SettingsHelper::getUserLanguage($user); // 'vi' hoặc 'en'
        
        // Lấy timezone của user
        $timezone = SettingsHelper::getUserTimezone($user); // 'Asia/Ho_Chi_Minh'
        
        // Chuyển đổi thời gian theo timezone của user
        $dateTime = Carbon::now();
        $userDateTime = SettingsHelper::convertToUserTimezone($dateTime, $user);
        
        // Format thời gian theo ngôn ngữ và timezone của user
        $formatted = SettingsHelper::formatDateTimeForUser($dateTime, $user);
        // Kết quả: "25/12/2024 14:30" (nếu language = 'vi')
    }

    /**
     * VÍ DỤ 7: Kiểm tra privacy level
     */
    public static function exampleCheckPrivacy()
    {
        $owner = User::find(1);
        $viewer = User::find(2);
        
        // Kiểm tra xem viewer có thể xem profile của owner không
        if (SettingsHelper::canAccess($owner, $viewer)) {
            // Hiển thị thông tin
        } else {
            // Ẩn thông tin
        }
    }

    /**
     * VÍ DỤ 8: Kiểm tra allow AI learning
     */
    public static function exampleCheckAILearning()
    {
        $user = User::find(1);
        
        // Trong AIConsultationService, kiểm tra trước khi lưu dữ liệu học tập
        if (SettingsHelper::allowAILearning($user)) {
            // Lưu interaction để cải thiện AI
            // Log::info('Saving interaction for AI learning', ['user_id' => $user->id]);
        } else {
            // Không lưu interaction
        }
    }

    /**
     * VÍ DỤ 9: Kiểm tra share health data
     */
    public static function exampleCheckShareHealthData()
    {
        $user = User::find(1);
        
        // Khi export dữ liệu cho nghiên cứu
        if (SettingsHelper::canShareHealthData($user)) {
            // Thêm dữ liệu đã ẩn danh vào dataset nghiên cứu
            // $anonymizedData = anonymize($user->healthData);
            // ResearchDataset::add($anonymizedData);
        }
    }

    /**
     * VÍ DỤ 10: Sử dụng trong Controller
     */
    public static function exampleInController()
    {
        // Trong bất kỳ controller nào:
        /*
        use App\Services\NotificationService;
        use App\Helpers\SettingsHelper;
        
        public function someAction(Request $request)
        {
            $user = Auth::user();
            $notificationService = app(NotificationService::class);
            
            // Gửi thông báo nếu user cho phép
            if ($notificationService->shouldNotify($user, 'email')) {
                $notificationService->sendEmailNotification(
                    $user,
                    'Tiêu đề',
                    'Nội dung',
                    'type'
                );
            }
            
            // Sử dụng timezone của user
            $userTimezone = SettingsHelper::getUserTimezone($user);
            $dateTime = Carbon::now($userTimezone);
            
            return view('some.view', compact('dateTime'));
        }
        */
    }
}

