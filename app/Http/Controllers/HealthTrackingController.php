<?php

namespace App\Http\Controllers;

use App\Models\HealthMetric;
use App\Models\Reminder;
use App\Models\AIConsultation;
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
            'recorded_date' => 'required|date',
            'weight' => 'nullable|numeric|min:20|max:300',
            'height' => 'nullable|numeric|min:50|max:250',
            'blood_pressure_systolic' => 'nullable|numeric|min:50|max:250',
            'blood_pressure_diastolic' => 'nullable|numeric|min:30|max:150',
            'blood_sugar' => 'nullable|numeric|min:50|max:500',
            'heart_rate' => 'nullable|numeric|min:40|max:200',
            'notes' => 'nullable|string|max:1000',
        ]);

        $metric = HealthMetric::create([
            'user_id' => Auth::id(),
            'recorded_date' => $validated['recorded_date'],
            'weight' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'blood_pressure_systolic' => $validated['blood_pressure_systolic'] ?? null,
            'blood_pressure_diastolic' => $validated['blood_pressure_diastolic'] ?? null,
            'blood_sugar' => $validated['blood_sugar'] ?? null,
            'heart_rate' => $validated['heart_rate'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Health metric recorded successfully!');
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

        return back()->with('success', 'Reminder created successfully!');
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

        $reminder->is_active = $validated['is_active'];
        $reminder->save();

        return back()->with('success', 'Reminder updated!');
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
