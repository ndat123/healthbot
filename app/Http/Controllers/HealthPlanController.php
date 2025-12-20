<?php

namespace App\Http\Controllers;

use App\Models\HealthProfile;
use App\Models\HealthPlan;
use App\Services\HealthPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HealthPlanController extends Controller
{
    protected $healthPlanService;

    public function __construct(HealthPlanService $healthPlanService)
    {
        $this->healthPlanService = $healthPlanService;
    }

    /**
     * Show health profile form or existing profile
     */
    public function index()
    {
        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();
        $plans = HealthPlan::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('health-plans.index', compact('profile', 'plans'));
    }

    /**
     * Show/create health profile
     */
    public function createProfile()
    {
        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();

        return view('health-plans.profile', compact('profile'));
    }

    /**
     * Store or update health profile
     */
    public function storeProfile(Request $request)
    {
        $validated = $request->validate([
            'age' => 'nullable|integer|min:1|max:120',
            'gender' => 'nullable|in:male,female,other',
            'height' => 'nullable|numeric|min:50|max:250',
            'weight' => 'nullable|numeric|min:20|max:300',
            'medical_history' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:500',
            'exercise_frequency' => 'nullable|string',
            'sleep_hours' => 'nullable|integer|min:0|max:24',
            'smoking' => 'nullable|boolean',
            'alcohol' => 'nullable|string',
            'health_goals' => 'nullable|array',
            'blood_pressure_systolic' => 'nullable|numeric|min:50|max:250',
            'blood_pressure_diastolic' => 'nullable|numeric|min:30|max:150',
            'blood_sugar' => 'nullable|numeric|min:50|max:500',
        ]);

        $user = Auth::user();
        
        // Prepare lifestyle habits
        $lifestyleHabits = [
            'exercise_frequency' => $request->exercise_frequency,
            'sleep_hours' => $request->sleep_hours,
            'smoking' => $request->has('smoking') ? (bool)$request->smoking : false,
            'alcohol' => $request->alcohol,
        ];

        $profile = HealthProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'age' => $validated['age'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'medical_history' => $validated['medical_history'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'lifestyle_habits' => $lifestyleHabits,
                'health_goals' => $validated['health_goals'] ?? null,
                'blood_pressure_systolic' => $validated['blood_pressure_systolic'] ?? null,
                'blood_pressure_diastolic' => $validated['blood_pressure_diastolic'] ?? null,
                'blood_sugar' => $validated['blood_sugar'] ?? null,
            ]
        );

        return redirect()->route('health-plans.index')
            ->with('success', 'Health profile saved successfully!');
    }

    /**
     * Generate health plan
     */
    public function generatePlan(Request $request)
    {
        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return redirect()->route('health-plans.profile')
                ->with('error', 'Please create your health profile first.');
        }

        $durationDays = $request->input('duration_days', 7);
        $durationDays = max(7, min(30, $durationDays)); // Between 7 and 30 days

        try {
            // Generate plan hoàn toàn từ AI (không dùng kế hoạch cứng)
            $planData = $this->healthPlanService->generatePlan($profile, $durationDays);
        } catch (\Throwable $e) {
            Log::error('Failed to generate AI health plan', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể tạo kế hoạch sức khỏe từ AI. Vui lòng thử lại sau hoặc kiểm tra API Key.');
        }

        // Create health plan
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays($durationDays - 1);

        $plan = HealthPlan::create([
            'user_id' => $user->id,
            'health_profile_id' => $profile->id,
            'title' => "{$durationDays}-Day Health Plan - " . Carbon::now()->format('M Y'),
            'plan_data' => $planData['plan_data'],
            'duration_days' => $durationDays,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'progress_data' => ['daily_progress' => []],
            'ai_prompt_used' => $planData['ai_prompt'],
            'ai_response' => $planData['ai_response'],
        ]);

        return redirect()->route('health-plans.show', $plan->id)
            ->with('success', 'Your personalized health plan has been generated!');
    }

    /**
     * Show health plan details
     */
    public function show($id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);
        $profile = $plan->healthProfile;

        return view('health-plans.show', compact('plan', 'profile'));
    }

    /**
     * Update progress
     */
    public function updateProgress(Request $request, $id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'day' => 'required|integer|min:1|max:' . $plan->duration_days,
            'completed' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
            'weight' => 'nullable|numeric',
            'mood' => 'nullable|string',
        ]);

        $progressData = $plan->progress_data ?? ['daily_progress' => []];
        
        $dayIndex = $validated['day'] - 1;
        $progressData['daily_progress'][$dayIndex] = [
            'day' => $validated['day'],
            'completed' => $validated['completed'],
            'notes' => $validated['notes'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'mood' => $validated['mood'] ?? null,
            'updated_at' => now()->toDateTimeString(),
        ];

        $plan->progress_data = $progressData;
        $plan->updateCompletionPercentage();
        $plan->save();

        return back()->with('success', 'Progress updated successfully!');
    }

    /**
     * Update plan status
     */
    public function updateStatus(Request $request, $id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,paused,completed,cancelled',
        ]);

        $plan->status = $validated['status'];
        $plan->save();

        return back()->with('success', 'Plan status updated!');
    }
}

