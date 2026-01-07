<?php

namespace App\Http\Controllers;

use App\Models\HealthMetric;
use App\Models\Reminder;
use App\Models\AIConsultation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HealthTrackingController extends Controller
{
    /**
     * Show health tracking dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get recent metrics
        $recentMetrics = HealthMetric::where('user_id', $user->id)
            ->orderBy('recorded_date', 'desc')
            ->limit(30)
            ->get();

        // Get active reminders
        $reminders = Reminder::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('reminder_time')
            ->get();

        // Get AI consultation history
        $consultations = AIConsultation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Prepare chart data
        $chartData = $this->prepareChartData($recentMetrics);

        return view('health-tracking.index', compact('recentMetrics', 'reminders', 'consultations', 'chartData'));
    }

    /**
     * Store health metric
     */
    public function storeMetric(Request $request)
    {
        $validated = $request->validate([
            'recorded_date' => 'required|date|before_or_equal:today',
            'weight' => 'nullable|numeric|min:20|max:300',
            'height' => 'nullable|numeric|min:50|max:250',
            'blood_pressure_systolic' => 'nullable|numeric|min:50|max:250',
            'blood_pressure_diastolic' => 'nullable|numeric|min:30|max:150',
            'blood_sugar' => 'nullable|numeric|min:50|max:500',
            'heart_rate' => 'nullable|numeric|min:40|max:200',
            'notes' => 'nullable|string|max:1000',
        ]);

        $recordedDate = Carbon::parse($validated['recorded_date'])->format('Y-m-d');
        
        // Check if metric already exists for this date
        $existingMetric = HealthMetric::where('user_id', Auth::id())
            ->whereDate('recorded_date', $recordedDate)
            ->first();

        if ($existingMetric) {
            // Update existing metric
            $existingMetric->update([
                'weight' => $validated['weight'] ?? $existingMetric->weight,
                'height' => $validated['height'] ?? $existingMetric->height,
                'blood_pressure_systolic' => $validated['blood_pressure_systolic'] ?? $existingMetric->blood_pressure_systolic,
                'blood_pressure_diastolic' => $validated['blood_pressure_diastolic'] ?? $existingMetric->blood_pressure_diastolic,
                'blood_sugar' => $validated['blood_sugar'] ?? $existingMetric->blood_sugar,
                'heart_rate' => $validated['heart_rate'] ?? $existingMetric->heart_rate,
                'notes' => $validated['notes'] ?? $existingMetric->notes,
            ]);
            
            $message = 'Chỉ số sức khỏe cho ngày ' . Carbon::parse($recordedDate)->format('d/m/Y') . ' đã được cập nhật!';
        } else {
            // Create new metric
            HealthMetric::create([
                'user_id' => Auth::id(),
                'recorded_date' => $recordedDate,
                'weight' => $validated['weight'] ?? null,
                'height' => $validated['height'] ?? null,
                'blood_pressure_systolic' => $validated['blood_pressure_systolic'] ?? null,
                'blood_pressure_diastolic' => $validated['blood_pressure_diastolic'] ?? null,
                'blood_sugar' => $validated['blood_sugar'] ?? null,
                'heart_rate' => $validated['heart_rate'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
            
            $message = 'Chỉ số sức khỏe đã được ghi nhận thành công!';
        }

        return back()->with('success', $message);
    }

    /**
     * Store reminder
     */
    public function storeReminder(Request $request)
    {
        $validated = $request->validate([
            'reminder_type' => 'required|in:medication,water,exercise,meal,appointment,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'reminder_time' => 'required|date_format:H:i',
            'reminder_days' => 'nullable|array',
            'reminder_days.*' => 'integer|min:0|max:6',
            'is_recurring' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $reminder = Reminder::create([
            'user_id' => Auth::id(),
            'reminder_type' => $validated['reminder_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'reminder_time' => $validated['reminder_time'],
            'reminder_days' => $validated['reminder_days'] ?? [0,1,2,3,4,5,6], // All days by default
            'is_recurring' => $validated['is_recurring'] ?? true,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        // Tạo thông báo khi reminder được tạo
        $user = Auth::user();
        $notificationService = app(NotificationService::class);
        
        $reminderTypeLabels = [
            'medication' => 'Thuốc',
            'water' => 'Nước',
            'exercise' => 'Tập thể dục',
            'meal' => 'Bữa ăn',
            'appointment' => 'Cuộc hẹn',
            'other' => 'Khác',
        ];
        
        $reminderTypeLabel = $reminderTypeLabels[$reminder->reminder_type] ?? 'Nhắc nhở';
        $recurringText = $reminder->is_recurring ? ' (Lặp lại)' : '';
        
        $title = "Nhắc nhở mới đã được tạo";
        $message = "Nhắc nhở '{$reminder->title}' đã được tạo thành công. Thời gian: {$reminder->reminder_time}";
        
        $notificationService->createNotification(
            $user,
            $title,
            $message,
            'reminder',
            route('health-tracking.index'),
            [
                'reminder_id' => $reminder->id,
                'reminder_type' => $reminder->reminder_type,
                'reminder_time' => $reminder->reminder_time,
                'is_recurring' => $reminder->is_recurring,
            ]
        );

        return back()->with('success', 'Nhắc nhở đã được tạo thành công!');
    }

    /**
     * Update reminder status
     */
    public function updateReminder(Request $request, $id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $oldStatus = $reminder->is_active;
        $reminder->is_active = $validated['is_active'];
        $reminder->save();

        // Tạo thông báo khi reminder được toggle
        $user = Auth::user();
        $notificationService = app(NotificationService::class);
        
        $reminderTypeLabels = [
            'medication' => 'Thuốc',
            'water' => 'Nước',
            'exercise' => 'Tập thể dục',
            'meal' => 'Bữa ăn',
            'appointment' => 'Cuộc hẹn',
            'other' => 'Khác',
        ];
        
        $reminderTypeLabel = $reminderTypeLabels[$reminder->reminder_type] ?? 'Nhắc nhở';
        
        if ($reminder->is_active) {
            // Reminder được kích hoạt
            $title = "Nhắc nhở đã được kích hoạt";
            $message = "Nhắc nhở '{$reminder->title}' ({$reminderTypeLabel}) đã được kích hoạt. Thời gian: {$reminder->reminder_time}";
        } else {
            // Reminder được tắt
            $title = "Nhắc nhở đã được tắt";
            $message = "Nhắc nhở '{$reminder->title}' ({$reminderTypeLabel}) đã được tắt.";
        }
        
        $notificationService->createNotification(
            $user,
            $title,
            $message,
            'reminder',
            route('health-tracking.index'),
            [
                'reminder_id' => $reminder->id,
                'reminder_type' => $reminder->reminder_type,
                'reminder_time' => $reminder->reminder_time,
            ]
        );

        return back()->with('success', 'Nhắc nhở đã được cập nhật!');
    }

    /**
     * Delete reminder
     */
    public function deleteReminder($id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);
        $reminder->delete();

        return back()->with('success', 'Reminder deleted!');
    }

    /**
     * Get metrics for chart (API)
     */
    public function getMetricsData(Request $request)
    {
        $user = Auth::user();
        $days = $request->input('days', 30);
        
        $metrics = HealthMetric::where('user_id', $user->id)
            ->where('recorded_date', '>=', Carbon::today()->subDays($days))
            ->orderBy('recorded_date', 'asc')
            ->get();

        $chartData = $this->prepareChartData($metrics);

        return response()->json($chartData);
    }

    /**
     * Prepare chart data from metrics
     */
    private function prepareChartData($metrics)
    {
        $dates = [];
        $weights = [];
        $bmis = [];
        $bloodPressures = [];
        $bloodSugars = [];

        foreach ($metrics as $metric) {
            $dates[] = $metric->recorded_date->format('M d');
            $weights[] = $metric->weight;
            $bmis[] = $metric->bmi;
            if ($metric->blood_pressure_systolic) {
                $bloodPressures[] = [
                    'systolic' => $metric->blood_pressure_systolic,
                    'diastolic' => $metric->blood_pressure_diastolic,
                ];
            } else {
                $bloodPressures[] = null;
            }
            $bloodSugars[] = $metric->blood_sugar;
        }

        return [
            'labels' => $dates,
            'weight' => $weights,
            'bmi' => $bmis,
            'blood_pressure' => $bloodPressures,
            'blood_sugar' => $bloodSugars,
        ];
    }
}
