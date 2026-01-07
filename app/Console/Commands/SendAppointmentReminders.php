<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Consultation;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders {--test : Test mode - hiển thị tất cả appointments sắp đến}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi thông báo nhắc nhở cho appointments sắp đến (trước 5 phút)';

    protected $notificationService;

    /**
     * Create a new command instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang kiểm tra appointments sắp đến...');

        $now = Carbon::now();
        $targetTime = $now->copy()->addMinutes(5); // 5 phút từ bây giờ

        $this->info("Thời gian hiện tại: {$now->format('Y-m-d H:i:s')}");
        $this->info("Đang tìm appointments vào lúc: {$targetTime->format('Y-m-d H:i:s')}");

        // Tìm tất cả appointments scheduled hôm nay chưa gửi reminder
        $allAppointments = Consultation::where('status', 'scheduled')
            ->whereDate('consultation_date', $now->format('Y-m-d')) // Chỉ kiểm tra appointments hôm nay
            ->whereNull('reminder_sent_at') // Chưa gửi reminder
            ->get();

        $this->info("Tìm thấy {$allAppointments->count()} appointments scheduled hôm nay chưa gửi reminder");

        // Filter appointments sắp đến trong 5 phút
        $upcomingAppointments = $allAppointments->filter(function ($appointment) use ($now, $targetTime) {
            try {
                // Parse consultation_time
                $appointmentTime = is_string($appointment->consultation_time) 
                    ? Carbon::parse($appointment->consultation_time)
                    : Carbon::parse($appointment->consultation_time->format('H:i:s'));
                
                $appointmentDateTime = Carbon::parse($appointment->consultation_date)
                    ->setTime($appointmentTime->hour, $appointmentTime->minute, 0);
                
                // Kiểm tra xem appointment có đúng vào thời điểm target (5 phút từ bây giờ) không
                // So sánh đến phút (bỏ qua giây)
                $appointmentMinute = $appointmentDateTime->format('Y-m-d H:i');
                $targetMinute = $targetTime->format('Y-m-d H:i');
                
                $matches = $appointmentMinute === $targetMinute;
                
                if (!$matches) {
                    $this->line("  Appointment ID {$appointment->id}: {$appointmentMinute} != {$targetMinute} (bỏ qua)");
                } else {
                    $this->info("  ✓ Appointment ID {$appointment->id}: {$appointmentMinute} == {$targetMinute} (MATCH!)");
                }
                
                return $matches;
            } catch (\Exception $e) {
                $this->warn("  Lỗi parse appointment ID {$appointment->id}: " . $e->getMessage());
                return false;
            }
        });

        $sent = 0;
        $failed = 0;

        foreach ($upcomingAppointments as $appointment) {
            try {
                $user = $appointment->user;
                
                if (!$user) {
                    $this->warn("Không tìm thấy user cho appointment ID: {$appointment->id}");
                    $failed++;
                    continue;
                }

                // Kiểm tra xem user có bật appointment reminders không
                $settings = $user->settings;
                if ($settings && !$settings->appointment_reminders) {
                    $this->info("User {$user->id} đã tắt appointment reminders, bỏ qua.");
                    continue;
                }

                // Format thời gian
                $appointmentTime = is_string($appointment->consultation_time) 
                    ? Carbon::parse($appointment->consultation_time)
                    : Carbon::parse($appointment->consultation_time->format('H:i:s'));
                
                $formattedDate = $appointment->consultation_date->format('d/m/Y');
                $formattedTime = $appointmentTime->format('H:i');
                
                // Lấy thông tin bác sĩ
                $doctorName = 'Bác sĩ';
                if ($appointment->doctor) {
                    $doctorName = $appointment->doctor->name ?? 'Bác sĩ';
                }

                $typeLabels = [
                    'in-person' => 'Trực tiếp',
                    'video' => 'Video call',
                    'phone' => 'Điện thoại',
                    'ai' => 'AI',
                ];
                $typeLabel = $typeLabels[$appointment->type] ?? $appointment->type;

                $title = "⏰ Nhắc nhở cuộc hẹn sắp đến";
                $message = "Bạn có cuộc hẹn với {$doctorName} vào lúc {$formattedTime} ngày {$formattedDate} ({$typeLabel}). Còn 5 phút nữa!";

                // Tạo notification
                $this->notificationService->createNotification(
                    $user,
                    $title,
                    $message,
                    'appointment',
                    route('doctor.appointment.show', $appointment->id),
                    [
                        'appointment_id' => $appointment->id,
                        'appointment_date' => $appointment->consultation_date->format('Y-m-d'),
                        'appointment_time' => $appointmentTime->format('H:i:s'),
                        'doctor_id' => $appointment->doctor_id,
                    ]
                );

                // Đánh dấu đã gửi reminder
                $appointment->reminder_sent_at = $now;
                $appointment->save();

                $sent++;
                $this->info("Đã gửi reminder cho appointment ID: {$appointment->id} - User: {$user->name}");

            } catch (\Exception $e) {
                $failed++;
                Log::error("Lỗi khi gửi reminder cho appointment ID: {$appointment->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->error("Lỗi khi gửi reminder cho appointment ID: {$appointment->id}: " . $e->getMessage());
            }
        }

        $this->info("Hoàn thành! Đã gửi: {$sent} reminders, Thất bại: {$failed}");

        // Test mode: hiển thị tất cả appointments sắp đến trong 30 phút
        if ($this->option('test')) {
            $this->info("\n=== TEST MODE: Tất cả appointments sắp đến trong 30 phút ===");
            $testAppointments = Consultation::where('status', 'scheduled')
                ->whereDate('consultation_date', '>=', $now->format('Y-m-d'))
                ->orderBy('consultation_date')
                ->orderBy('consultation_time')
                ->limit(20)
                ->get();

            foreach ($testAppointments as $apt) {
                try {
                    $aptTime = is_string($apt->consultation_time) 
                        ? Carbon::parse($apt->consultation_time)
                        : Carbon::parse($apt->consultation_time->format('H:i:s'));
                    
                    $aptDateTime = Carbon::parse($apt->consultation_date)
                        ->setTime($aptTime->hour, $aptTime->minute, 0);
                    
                    $diffMinutes = $now->diffInMinutes($aptDateTime, false);
                    
                    if ($diffMinutes >= 0 && $diffMinutes <= 30) {
                        $reminderSent = $apt->reminder_sent_at ? '✓ Đã gửi' : '✗ Chưa gửi';
                        $this->line("  ID: {$apt->id} | {$aptDateTime->format('Y-m-d H:i')} | Còn {$diffMinutes} phút | {$reminderSent}");
                    }
                } catch (\Exception $e) {
                    $this->warn("  Lỗi parse appointment ID {$apt->id}");
                }
            }
        }

        return Command::SUCCESS;
    }
}

