<?php

namespace App\Http\Controllers;

use App\Models\HealthProfile;
use App\Models\HealthPlan;
use App\Services\HealthPlanService;
use App\Services\GeminiService;
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

    /**
     * Update meal completion status
     */
    public function updateMealCompletion(Request $request, $id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'day' => 'required|integer|min:1|max:' . $plan->duration_days,
            'meal_index' => 'required|integer|min:0',
            'meal_key' => 'required|string',
            'completed' => 'required|boolean',
        ]);

        $progressData = $plan->progress_data ?? ['daily_progress' => []];
        $dayIndex = $validated['day'] - 1;
        
        // Initialize daily progress if not exists
        if (!isset($progressData['daily_progress'][$dayIndex])) {
            $progressData['daily_progress'][$dayIndex] = [
                'day' => $validated['day'],
                'completed' => false,
                'meals' => [],
            ];
        }
        
        // Initialize meals array if not exists
        if (!isset($progressData['daily_progress'][$dayIndex]['meals'])) {
            $progressData['daily_progress'][$dayIndex]['meals'] = [];
        }
        
        // Update meal completion status
        $progressData['daily_progress'][$dayIndex]['meals'][$validated['meal_key']] = $validated['completed'];
        
        // Handle exercise completion if is_exercise flag is set
        $isExercise = $request->input('is_exercise', false);
        if ($isExercise) {
            if (!isset($progressData['daily_progress'][$dayIndex]['exercises'])) {
                $progressData['daily_progress'][$dayIndex]['exercises'] = [];
            }
            $progressData['daily_progress'][$dayIndex]['exercises'][$validated['meal_key']] = $validated['completed'];
        }
        
        // Check if all meals for the day are completed
        $dayPlan = $plan->plan_data['daily_plans'][$dayIndex] ?? null;
        $allActivitiesCompleted = false;
        
        if ($dayPlan) {
            // Check meals
            $allMealsCompleted = true;
            if (isset($dayPlan['meals'])) {
                foreach ($dayPlan['meals'] as $mealIndex => $meal) {
                    $mealKey = 'meal_' . $validated['day'] . '_' . $mealIndex;
                    if (!isset($progressData['daily_progress'][$dayIndex]['meals'][$mealKey]) || 
                        !$progressData['daily_progress'][$dayIndex]['meals'][$mealKey]) {
                        $allMealsCompleted = false;
                        break;
                    }
                }
            } else {
                $allMealsCompleted = true; // No meals to check
            }
            
            // Check exercises
            $allExercisesCompleted = true;
            if (isset($dayPlan['exercises'])) {
                if (!isset($progressData['daily_progress'][$dayIndex]['exercises'])) {
                    $progressData['daily_progress'][$dayIndex]['exercises'] = [];
                }
                foreach ($dayPlan['exercises'] as $exerciseIndex => $exercise) {
                    $exerciseKey = 'exercise_' . $validated['day'] . '_' . $exerciseIndex;
                    if (!isset($progressData['daily_progress'][$dayIndex]['exercises'][$exerciseKey]) || 
                        !$progressData['daily_progress'][$dayIndex]['exercises'][$exerciseKey]) {
                        $allExercisesCompleted = false;
                        break;
                    }
                }
            } else {
                $allExercisesCompleted = true; // No exercises to check
            }
            
            // Check if all activities are completed
            $allActivitiesCompleted = $allMealsCompleted && $allExercisesCompleted;
            
            // Auto-mark day as completed if all activities are done
            if ($allActivitiesCompleted && !($progressData['daily_progress'][$dayIndex]['completed'] ?? false)) {
                $progressData['daily_progress'][$dayIndex]['completed'] = true;
                $progressData['daily_progress'][$dayIndex]['completed_at'] = now()->toDateTimeString();
            }
        }
        
        $plan->progress_data = $progressData;
        $plan->updateCompletionPercentage();
        $plan->save();

        // Check if a week (7 days) is completed
        $weekCompleted = false;
        $weekNumber = null;
        if ($allActivitiesCompleted) {
            $weekNumber = ceil($validated['day'] / 7);
            $weekStartDay = ($weekNumber - 1) * 7 + 1;
            $weekEndDay = min($weekNumber * 7, $plan->duration_days);
            
            // Check if all days in the week are completed
            $allWeekDaysCompleted = true;
            for ($day = $weekStartDay; $day <= $weekEndDay; $day++) {
                $dayIdx = $day - 1;
                if (!isset($progressData['daily_progress'][$dayIdx]['completed']) || 
                    !$progressData['daily_progress'][$dayIdx]['completed']) {
                    $allWeekDaysCompleted = false;
                    break;
                }
            }
            
            // Check if this week hasn't been processed yet
            if ($allWeekDaysCompleted) {
                if (!isset($progressData['weekly_progress'][$weekNumber]['processed']) || 
                    !$progressData['weekly_progress'][$weekNumber]['processed']) {
                    $weekCompleted = true;
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'completion_percentage' => $plan->completion_percentage,
            'all_activities_completed' => $allActivitiesCompleted,
            'week_completed' => $weekCompleted,
            'week_number' => $weekNumber,
            'day' => $validated['day'],
            'message' => 'Meal completion updated successfully!'
        ]);
    }

    /**
     * Save user's meal selection (which option they chose)
     */
    public function saveMealSelection(Request $request, $id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'meal_key' => 'required|string',
            'option_index' => 'required|integer|min:0',
        ]);

        $progressData = $plan->progress_data ?? ['daily_progress' => []];
        
        // Initialize meal_selections if not exists
        if (!isset($progressData['meal_selections'])) {
            $progressData['meal_selections'] = [];
        }
        
        // Save the selected option
        $progressData['meal_selections'][$validated['meal_key']] = $validated['option_index'];
        
        $plan->progress_data = $progressData;
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Meal selection saved successfully!'
        ]);
    }

    /**
     * Complete all activities for a day
     */
    public function completeAllActivities(Request $request, $id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'day' => 'required|integer|min:1|max:' . $plan->duration_days,
        ]);

        $dayNumber = $validated['day'];
        $dayIndex = $dayNumber - 1;
        $dayPlan = $plan->plan_data['daily_plans'][$dayIndex] ?? null;

        if (!$dayPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Day plan not found'
            ], 404);
        }

        $progressData = $plan->progress_data ?? ['daily_progress' => []];
        
        // Initialize daily progress if not exists
        if (!isset($progressData['daily_progress'][$dayIndex])) {
            $progressData['daily_progress'][$dayIndex] = [
                'day' => $dayNumber,
                'completed' => false,
                'meals' => [],
                'exercises' => [],
            ];
        }

        // Initialize meals and exercises arrays if not exists
        if (!isset($progressData['daily_progress'][$dayIndex]['meals'])) {
            $progressData['daily_progress'][$dayIndex]['meals'] = [];
        }
        if (!isset($progressData['daily_progress'][$dayIndex]['exercises'])) {
            $progressData['daily_progress'][$dayIndex]['exercises'] = [];
        }

        // Mark all meals as completed
        if (isset($dayPlan['meals'])) {
            foreach ($dayPlan['meals'] as $mealIndex => $meal) {
                $mealKey = 'meal_' . $dayNumber . '_' . $mealIndex;
                $progressData['daily_progress'][$dayIndex]['meals'][$mealKey] = true;
            }
        }

        // Mark all exercises as completed
        if (isset($dayPlan['exercises'])) {
            foreach ($dayPlan['exercises'] as $exerciseIndex => $exercise) {
                $exerciseKey = 'exercise_' . $dayNumber . '_' . $exerciseIndex;
                $progressData['daily_progress'][$dayIndex]['exercises'][$exerciseKey] = true;
            }
        }

        // Mark day as completed
        $progressData['daily_progress'][$dayIndex]['completed'] = true;
        $progressData['daily_progress'][$dayIndex]['completed_at'] = now()->toDateTimeString();

        $plan->progress_data = $progressData;
        $plan->updateCompletionPercentage();
        $plan->save();

        // Check if a week (7 days) is completed
        $weekCompleted = false;
        $weekNumber = null;
        $weekNumber = ceil($dayNumber / 7);
        $weekStartDay = ($weekNumber - 1) * 7 + 1;
        $weekEndDay = min($weekNumber * 7, $plan->duration_days);
        
        // Check if all days in the week are completed
        $allWeekDaysCompleted = true;
        for ($day = $weekStartDay; $day <= $weekEndDay; $day++) {
            $dayIdx = $day - 1;
            if (!isset($progressData['daily_progress'][$dayIdx]['completed']) || 
                !$progressData['daily_progress'][$dayIdx]['completed']) {
                $allWeekDaysCompleted = false;
                break;
            }
        }
        
        // Check if this week hasn't been processed yet
        if ($allWeekDaysCompleted) {
            if (!isset($progressData['weekly_progress'][$weekNumber]['processed']) || 
                !$progressData['weekly_progress'][$weekNumber]['processed']) {
                $weekCompleted = true;
            }
        }

        return response()->json([
            'success' => true,
            'completion_percentage' => $plan->completion_percentage,
            'week_completed' => $weekCompleted,
            'week_number' => $weekNumber,
            'day' => $dayNumber,
            'message' => 'All activities completed successfully!'
        ]);
    }

    /**
     * Adjust plan when user can't follow the schedule
     */
    public function adjustPlan(Request $request, $id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|max:' . $plan->duration_days,
            'reason' => 'required|string|max:500',
            'weight' => 'nullable|numeric|min:0|max:500',
            'mood' => 'nullable|string',
            'actual_meals' => 'nullable|string|max:1000',
            'actual_exercises' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $dayNumber = $validated['day_number'];
        $progressData = $plan->progress_data ?? ['daily_progress' => []];
        $dayIndex = $dayNumber - 1;
        
        // Save adjustment information for this day
        if (!isset($progressData['daily_progress'][$dayIndex])) {
            $progressData['daily_progress'][$dayIndex] = ['day' => $dayNumber];
        }
        
        $progressData['daily_progress'][$dayIndex]['adjustment_reason'] = $validated['reason'];
        $progressData['daily_progress'][$dayIndex]['actual_meals'] = $validated['actual_meals'];
        $progressData['daily_progress'][$dayIndex]['actual_exercises'] = $validated['actual_exercises'];
        $progressData['daily_progress'][$dayIndex]['weight'] = $validated['weight'];
        $progressData['daily_progress'][$dayIndex]['mood'] = $validated['mood'];
        $progressData['daily_progress'][$dayIndex]['notes'] = $validated['notes'];
        $progressData['daily_progress'][$dayIndex]['adjusted_at'] = now()->toDateTimeString();
        
        // Clear meal selections for adjusted day and all days after
        if (isset($progressData['meal_selections'])) {
            foreach ($progressData['meal_selections'] as $mealKey => $selection) {
                // Extract day number from meal key (format: meal_X_Y)
                if (preg_match('/meal_(\d+)_/', $mealKey, $matches)) {
                    $mealDayNumber = (int)$matches[1];
                    if ($mealDayNumber >= $dayNumber) {
                        unset($progressData['meal_selections'][$mealKey]);
                    }
                }
            }
        }
        
        // Clear meal and exercise completion status for adjusted day and after
        for ($d = $dayNumber; $d <= $plan->duration_days; $d++) {
            $dIdx = $d - 1;
            if (isset($progressData['daily_progress'][$dIdx])) {
                unset($progressData['daily_progress'][$dIdx]['meals']);
                unset($progressData['daily_progress'][$dIdx]['exercises']);
                unset($progressData['daily_progress'][$dIdx]['completed']);
            }
        }
        
        // Get remaining days
        $remainingDays = $plan->duration_days - $dayNumber;
        
        if ($remainingDays > 0) {
            // Use AI to regenerate plan for remaining days
            $adjustmentSummary = $this->regenerateRemainingDays($plan, $dayNumber, $validated, $remainingDays);
            
            $plan->progress_data = $progressData;
            $plan->save();
            
            // Refresh model to ensure latest data
            $plan->refresh();
            
            return response()->json([
                'success' => true,
                'adjustment_summary' => $adjustmentSummary,
                'remaining_days' => $remainingDays,
                'message' => 'Kế hoạch đã được điều chỉnh thành công!'
            ]);
        } else {
            $plan->progress_data = $progressData;
            $plan->save();
            
            return response()->json([
                'success' => true,
                'adjustment_summary' => 'Đã lưu thông tin. Đây là ngày cuối cùng của kế hoạch.',
                'message' => 'Thông tin đã được lưu!'
            ]);
        }
    }

    /**
     * Regenerate plan for remaining days using AI
     */
    private function regenerateRemainingDays($plan, $fromDay, $adjustmentInfo, $remainingDays)
    {
        try {
            $geminiService = app(GeminiService::class);
            $profile = $plan->healthProfile;
            
            // Build context about what happened
            $prompt = "Bạn là chuyên gia sức khỏe AI. Người dùng đang theo kế hoạch sức khỏe nhưng NGÀY {$fromDay} không thể theo được.\n\n";
            
            $prompt .= "THÔNG TIN KẾ HOẠCH GỐC:\n";
            $prompt .= "- Tổng số ngày: {$plan->duration_days}\n";
            $prompt .= "- Ngày hiện tại: {$fromDay}\n";
            $prompt .= "- Số ngày còn lại: {$remainingDays}\n\n";
            
            if ($profile) {
                $prompt .= "HỒ SƠ:\n";
                $prompt .= "- Tuổi: {$profile->age}, Giới tính: {$profile->gender}, BMI: {$profile->bmi}\n";
                if ($profile->weight) $prompt .= "- Cân nặng: {$profile->weight}kg\n";
                if ($profile->health_goals) {
                    $goals = is_array($profile->health_goals) ? implode(', ', $profile->health_goals) : $profile->health_goals;
                    $prompt .= "- Mục tiêu: {$goals}\n";
                }
            }
            
            $prompt .= "\nTÌNH HÌNH NGÀY {$fromDay}:\n";
            $prompt .= "- Lý do không theo được: {$adjustmentInfo['reason']}\n";
            if ($adjustmentInfo['actual_meals']) {
                $prompt .= "- Bữa ăn thực tế: {$adjustmentInfo['actual_meals']}\n";
            }
            if ($adjustmentInfo['actual_exercises']) {
                $prompt .= "- Tập luyện thực tế: {$adjustmentInfo['actual_exercises']}\n";
            }
            if ($adjustmentInfo['weight']) {
                $prompt .= "- Cân nặng: {$adjustmentInfo['weight']}kg\n";
            }
            if ($adjustmentInfo['mood']) {
                $prompt .= "- Tâm trạng: {$adjustmentInfo['mood']}\n";
            }
            
            // AI only generates plans for days AFTER the adjustment day
            $totalDaysToGenerate = $remainingDays; // Only remaining days, not including adjustment day
            $nextDay = $fromDay + 1;
            
            $prompt .= "\nYÊU CẦU QUAN TRỌNG:\n";
            $prompt .= "1. PHẢI TẠO ĐỦ {$totalDaysToGenerate} NGÀY (từ ngày {$nextDay} đến ngày {$plan->duration_days})\n";
            $prompt .= "2. Ngày {$nextDay} trở đi phải điều chỉnh dựa trên tình hình ngày {$fromDay}: {$adjustmentInfo['reason']}\n";
            $prompt .= "3. Các ngày sau điều chỉnh dần trở lại kế hoạch bình thường\n";
            $prompt .= "4. Mỗi bữa ăn có 2-3 lựa chọn với calories\n";
            $prompt .= "5. TIẾNG VIỆT ngắn gọn\n";
            $prompt .= "6. Trả về JSON thuần (không markdown)\n\n";
            
            $prompt .= 'CẤU TRÚC JSON BẮT BUỘC (phải có ĐỦ ' . $totalDaysToGenerate . ' ngày từ ngày ' . $nextDay . ' đến ' . $plan->duration_days . '):\n{
  "daily_plans": [';
            
            // Generate example structure for remaining days (NOT including adjustment day)
            for ($d = $nextDay; $d <= $plan->duration_days; $d++) {
                if ($d > $nextDay) $prompt .= ',';
                $prompt .= '
    {
      "day": ' . $d . ',
      "meals": [
        {
          "time": "Bữa sáng",
          "options": [
            {"food": "Lựa chọn 1", "calories": 300},
            {"food": "Lựa chọn 2", "calories": 320}
          ]
        },
        {
          "time": "Bữa trưa",
          "options": [
            {"food": "Lựa chọn 1", "calories": 450},
            {"food": "Lựa chọn 2", "calories": 470}
          ]
        },
        {
          "time": "Bữa tối",
          "options": [
            {"food": "Lựa chọn 1", "calories": 400},
            {"food": "Lựa chọn 2", "calories": 420}
          ]
        }
      ],
      "exercises": [{"type": "Cardio", "name": "Đi bộ", "duration": 30}],
      "lifestyle": ["Uống đủ nước", "Ngủ đủ giấc"],
      "notes": "Ghi chú ngắn"
    }';
            }
            
            $prompt .= '
  ],
  "adjustment_summary": "Tóm tắt điều chỉnh"
}';
            
            $systemInstruction = "Chuyên gia sức khỏe AI. QUAN TRỌNG: Phải tạo CHÍNH XÁC {$totalDaysToGenerate} ngày trong daily_plans array, từ ngày {$nextDay} đến ngày {$plan->duration_days}. Điều chỉnh dựa trên tình hình ngày {$fromDay}: {$adjustmentInfo['reason']}. Trả JSON thuần không markdown. TIẾNG VIỆT.";
            
            Log::info('Requesting AI to adjust plan', [
                'from_day' => $fromDay,
                'to_day' => $plan->duration_days,
                'remaining_days' => $remainingDays
            ]);
            
            $response = $geminiService->generateJsonContent(
                $prompt,
                $systemInstruction,
                [],
                [
                    'temperature' => 0.8,
                    'max_tokens' => 16384,
                    'timeout' => 60,
                    'http_timeout' => 45,
                    'model' => 'gemini-2.5-flash'
                ]
            );
            
            Log::info('AI adjustment response received', ['response_length' => strlen($response)]);
            
            // Parse response
            $response = preg_replace('/```json\s*/s', '', $response);
            $response = preg_replace('/```\s*$/s', '', $response);
            $response = trim($response);
            
            $decoded = json_decode($response, true);
            
            if (!$decoded) {
                Log::error('Failed to parse AI adjustment response', ['response' => substr($response, 0, 500)]);
                throw new \RuntimeException('Invalid AI response format');
            }
            
            if (!isset($decoded['daily_plans']) || !is_array($decoded['daily_plans'])) {
                Log::error('AI response missing daily_plans', ['decoded' => $decoded]);
                throw new \RuntimeException('AI response missing daily_plans');
            }
            
            // Validate number of days returned
            $receivedDaysCount = count($decoded['daily_plans']);
            if ($receivedDaysCount < $totalDaysToGenerate) {
                Log::warning('AI returned fewer days than expected', [
                    'expected' => $totalDaysToGenerate,
                    'received' => $receivedDaysCount,
                    'from_day' => $fromDay,
                    'to_day' => $plan->duration_days
                ]);
            }
            
            // Update plan data with new days
            $planData = $plan->plan_data;
            
            // First, create plan for adjustment day based on actual data
            $adjustmentDayIdx = $fromDay - 1;
            $adjustmentDayPlan = $this->createAdjustmentDayPlan($fromDay, $adjustmentInfo);
            $planData['daily_plans'][$adjustmentDayIdx] = $adjustmentDayPlan;
            
            Log::info('Created adjustment day plan from actual data', [
                'day' => $fromDay,
                'index' => $adjustmentDayIdx
            ]);
            
            // Then update remaining days with AI-generated plans
            Log::info('Updating remaining days with AI plans', [
                'from_day' => $fromDay + 1,
                'remaining_days' => $remainingDays,
                'new_days_count' => $receivedDaysCount,
                'expected_days' => $totalDaysToGenerate
            ]);
            
            foreach ($decoded['daily_plans'] as $newDayPlan) {
                $dayNumber = $newDayPlan['day'];
                $dayIdx = $dayNumber - 1;
                
                // Update only days AFTER adjustment day
                if ($dayNumber > $fromDay && $dayNumber <= $plan->duration_days) {
                    $planData['daily_plans'][$dayIdx] = $newDayPlan;
                    Log::info('Updated day plan', ['day' => $dayNumber, 'index' => $dayIdx]);
                }
            }
            
            $plan->plan_data = $planData;
            $plan->save();
            
            // Refresh model to ensure latest data
            $plan->refresh();
            
            Log::info('Plan adjustment saved successfully');
            
            return $decoded['adjustment_summary'] ?? "Đã điều chỉnh kế hoạch {$remainingDays} ngày còn lại dựa trên tình hình thực tế của bạn.";
            
        } catch (\Exception $e) {
            Log::error('Failed to regenerate plan: ' . $e->getMessage());
            return "Đã lưu thông tin điều chỉnh. Vui lòng tiếp tục theo kế hoạch hiện tại hoặc tạo kế hoạch mới.";
        }
    }

    /**
     * Create adjustment day plan based on actual user data using AI analysis
     */
    private function createAdjustmentDayPlan($dayNumber, $adjustmentInfo)
    {
        try {
            $geminiService = app(GeminiService::class);
            
            // Build prompt for AI to analyze actual meals and create plan
            $prompt = "Phân tích bữa ăn và tập luyện thực tế của người dùng, ước tính calories và tạo gợi ý thay thế.\n\n";
            
            $prompt .= "THÔNG TIN THỰC TẾ NGÀY {$dayNumber}:\n";
            $prompt .= "- Lý do: {$adjustmentInfo['reason']}\n";
            
            if (!empty($adjustmentInfo['actual_meals'])) {
                $prompt .= "- Bữa ăn thực tế:\n{$adjustmentInfo['actual_meals']}\n";
            }
            
            if (!empty($adjustmentInfo['actual_exercises'])) {
                $prompt .= "- Tập luyện thực tế: {$adjustmentInfo['actual_exercises']}\n";
            }
            
            if (!empty($adjustmentInfo['weight'])) {
                $prompt .= "- Cân nặng: {$adjustmentInfo['weight']}kg\n";
            }
            
            if (!empty($adjustmentInfo['mood'])) {
                $prompt .= "- Tâm trạng: {$adjustmentInfo['mood']}\n";
            }
            
            $prompt .= "\nYÊU CẦU:\n";
            $prompt .= "1. Ước tính calories cho mỗi bữa ăn thực tế\n";
            $prompt .= "2. Tạo 1-2 gợi ý thay thế tương đương cho mỗi bữa (cùng calories)\n";
            $prompt .= "3. Nếu không có thông tin bữa nào, tạo gợi ý phù hợp với lý do điều chỉnh\n";
            $prompt .= "4. Phân tích tập luyện thực tế (nếu có)\n";
            $prompt .= "5. Đưa ra lời khuyên lifestyle phù hợp\n";
            $prompt .= "6. TIẾNG VIỆT ngắn gọn\n\n";
            
            $prompt .= 'Trả về JSON:\n{
  "day": ' . $dayNumber . ',
  "meals": [
    {
      "time": "Bữa sáng",
      "options": [
        {"food": "Món đã ăn", "calories": 400},
        {"food": "Gợi ý thay thế", "calories": 400}
      ]
    },
    {
      "time": "Bữa trưa",
      "options": [
        {"food": "Món đã ăn", "calories": 600},
        {"food": "Gợi ý thay thế", "calories": 600}
      ]
    },
    {
      "time": "Bữa tối",
      "options": [
        {"food": "Món đã ăn", "calories": 500},
        {"food": "Gợi ý thay thế", "calories": 500}
      ]
    }
  ],
  "exercises": [
    {"type": "Loại", "name": "Tên bài tập", "duration": 30}
  ],
  "lifestyle": ["Lời khuyên 1", "Lời khuyên 2"],
  "notes": "Ghi chú tổng hợp"
}';
            
            $systemInstruction = "Chuyên gia dinh dưỡng AI. Phân tích bữa ăn thực tế, ước tính calories chính xác. Trả JSON thuần không markdown. TIẾNG VIỆT.";
            
            $response = $geminiService->generateJsonContent(
                $prompt,
                $systemInstruction,
                [],
                [
                    'temperature' => 0.7,
                    'max_tokens' => 2048,
                    'timeout' => 30,
                    'http_timeout' => 25,
                    'model' => 'gemini-2.5-flash'
                ]
            );
            
            // Parse response
            $response = preg_replace('/```json\s*/s', '', $response);
            $response = preg_replace('/```\s*$/s', '', $response);
            $response = trim($response);
            
            $decoded = json_decode($response, true);
            
            if ($decoded && isset($decoded['meals'])) {
                Log::info('AI created adjustment day plan successfully', ['day' => $dayNumber]);
                return $decoded;
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to create adjustment day plan with AI: ' . $e->getMessage());
        }
        
        // Fallback: Create basic plan from parsed data
        return $this->createBasicAdjustmentDayPlan($dayNumber, $adjustmentInfo);
    }
    
    /**
     * Fallback: Create basic adjustment day plan without AI
     */
    private function createBasicAdjustmentDayPlan($dayNumber, $adjustmentInfo)
    {
        $plan = [
            'day' => $dayNumber,
            'meals' => [],
            'exercises' => [],
            'lifestyle' => [],
            'notes' => ''
        ];
        
        // Parse actual meals - keep exact food names
        $mealsData = [
            'Bữa sáng' => null,
            'Bữa trưa' => null,
            'Bữa tối' => null
        ];
        
        if (!empty($adjustmentInfo['actual_meals'])) {
            $mealsText = $adjustmentInfo['actual_meals'];
            $mealLines = explode("\n", $mealsText);
            
            foreach ($mealLines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Parse format: "Bữa sáng: Bánh mì trứng"
                if (preg_match('/^(Bữa\s+sáng):\s*(.+)$/ui', $line, $matches)) {
                    $mealsData['Bữa sáng'] = trim($matches[2]);
                } elseif (preg_match('/^(Bữa\s+trưa):\s*(.+)$/ui', $line, $matches)) {
                    $mealsData['Bữa trưa'] = trim($matches[2]);
                } elseif (preg_match('/^(Bữa\s+tối):\s*(.+)$/ui', $line, $matches)) {
                    $mealsData['Bữa tối'] = trim($matches[2]);
                }
            }
        }
        
        // Create meals with actual food names or defaults
        foreach ($mealsData as $time => $food) {
            $plan['meals'][] = [
                'time' => $time,
                'options' => [
                    [
                        'food' => $food ?: 'Chưa ghi nhận',
                        'calories' => $food ? $this->estimateCalories($time) : 0
                    ]
                ]
            ];
        }
        
        // Parse exercises
        if (!empty($adjustmentInfo['actual_exercises'])) {
            $exerciseText = $adjustmentInfo['actual_exercises'];
            $type = $name = '';
            $duration = 0;
            
            if (preg_match('/Loại:\s*([^,]+)/u', $exerciseText, $matches)) {
                $type = trim($matches[1]);
            }
            if (preg_match('/Bài tập:\s*([^,]+)/u', $exerciseText, $matches)) {
                $name = trim($matches[1]);
            }
            if (preg_match('/Thời gian:\s*(\d+)/u', $exerciseText, $matches)) {
                $duration = (int)$matches[1];
            }
            
            if ($type || $name) {
                $plan['exercises'][] = [
                    'type' => $type ?: 'Tập luyện',
                    'name' => $name ?: 'Hoạt động thể chất',
                    'duration' => $duration
                ];
            }
        } else {
            $plan['exercises'][] = [
                'type' => 'Nghỉ ngơi',
                'name' => 'Không tập luyện',
                'duration' => 0
            ];
        }
        
        // Lifestyle
        $plan['lifestyle'] = ['Ngày điều chỉnh do: ' . $adjustmentInfo['reason']];
        
        // Notes
        $notes = ['Ngày điều chỉnh'];
        if (!empty($adjustmentInfo['mood'])) {
            $moodMap = ['excellent' => 'Tuyệt vời', 'good' => 'Tốt', 'okay' => 'Ổn', 'poor' => 'Không tốt'];
            $notes[] = 'Tâm trạng: ' . ($moodMap[$adjustmentInfo['mood']] ?? $adjustmentInfo['mood']);
        }
        if (!empty($adjustmentInfo['weight'])) {
            $notes[] = 'Cân nặng: ' . $adjustmentInfo['weight'] . 'kg';
        }
        
        $plan['notes'] = implode('. ', $notes);
        
        return $plan;
    }
    
    /**
     * Estimate calories based on meal time (simple fallback)
     */
    private function estimateCalories($mealTime)
    {
        $estimates = [
            'Bữa sáng' => 450,
            'Bữa trưa' => 650,
            'Bữa tối' => 550
        ];
        
        return $estimates[$mealTime] ?? 500;
    }

    /**
     * Get AI advice when week is completed
     */
    public function getWeekCompletionAdvice(Request $request, $id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'week_number' => 'required|integer|min:1',
            'weight' => 'nullable|numeric|min:0|max:500',
            'mood' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $weekNumber = $validated['week_number'];
        $weekStartDay = ($weekNumber - 1) * 7 + 1;
        $weekEndDay = min($weekNumber * 7, $plan->duration_days);
        $profile = $plan->healthProfile;
        
        // Get all days in this week
        $weekDays = [];
        $weekProgress = [];
        for ($day = $weekStartDay; $day <= $weekEndDay; $day++) {
            $dayIndex = $day - 1;
            if (isset($plan->plan_data['daily_plans'][$dayIndex])) {
                $weekDays[] = $plan->plan_data['daily_plans'][$dayIndex];
                $weekProgress[] = $plan->progress_data['daily_progress'][$dayIndex] ?? [];
            }
        }

        // Initialize weekly progress
        $progressData = $plan->progress_data ?? ['daily_progress' => [], 'weekly_progress' => []];
        if (!isset($progressData['weekly_progress'])) {
            $progressData['weekly_progress'] = [];
        }
        
        // Update weekly metrics
        $progressData['weekly_progress'][$weekNumber] = [
            'week_number' => $weekNumber,
            'weight' => $validated['weight'] ?? null,
            'mood' => $validated['mood'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'week_start_day' => $weekStartDay,
            'week_end_day' => $weekEndDay,
            'metrics_updated_at' => now()->toDateTimeString(),
            'processed' => true,
        ];

        // Update health profile if weight is provided
        if ($validated['weight'] && $profile) {
            $profile->weight = $validated['weight'];
            $profile->save();
        }

        // Generate AI advice for the week
        $aiAdvice = $this->generateWeekCompletionAdvice($plan, $weekNumber, $weekDays, $weekProgress, $progressData['weekly_progress'][$weekNumber], $profile);

        // Save AI advice to weekly progress
        $progressData['weekly_progress'][$weekNumber]['ai_advice'] = $aiAdvice;
        $progressData['weekly_progress'][$weekNumber]['ai_advice_generated_at'] = now()->toDateTimeString();
        
        $plan->progress_data = $progressData;
        $plan->save();

        return response()->json([
            'success' => true,
            'ai_advice' => $aiAdvice,
            'week_number' => $weekNumber,
            'message' => 'Health metrics updated and AI advice generated for the week!'
        ]);
    }

    /**
     * Generate AI advice based on week completion
     */
    private function generateWeekCompletionAdvice($plan, $weekNumber, $weekDays, $weekProgress, $weekMetrics, $profile)
    {
        try {
            $geminiService = app(GeminiService::class);
            
            // Calculate statistics for the week
            $totalMeals = 0;
            $totalExercises = 0;
            $completedMeals = 0;
            $completedExercises = 0;
            $moods = [];
            $weights = [];
            $weekStartDay = ($weekNumber - 1) * 7 + 1;
            $weekEndDay = min($weekNumber * 7, $plan->duration_days);
            
            foreach ($weekDays as $index => $dayPlan) {
                if (isset($dayPlan['meals'])) {
                    $totalMeals += count($dayPlan['meals']);
                    foreach ($dayPlan['meals'] as $mealIdx => $meal) {
                        $dayNum = $dayPlan['day'] ?? ($weekStartDay + $index);
                        $mealKey = 'meal_' . $dayNum . '_' . $mealIdx;
                        if (isset($weekProgress[$index]['meals'][$mealKey]) && $weekProgress[$index]['meals'][$mealKey]) {
                            $completedMeals++;
                        }
                    }
                }
                if (isset($dayPlan['exercises'])) {
                    $totalExercises += count($dayPlan['exercises']);
                    foreach ($dayPlan['exercises'] as $exIdx => $exercise) {
                        $dayNum = $dayPlan['day'] ?? ($weekStartDay + $index);
                        $exKey = 'exercise_' . $dayNum . '_' . $exIdx;
                        if (isset($weekProgress[$index]['exercises'][$exKey]) && $weekProgress[$index]['exercises'][$exKey]) {
                            $completedExercises++;
                        }
                    }
                }
                if (isset($weekProgress[$index]['mood'])) {
                    $moods[] = $weekProgress[$index]['mood'];
                }
                if (isset($weekProgress[$index]['weight'])) {
                    $weights[] = $weekProgress[$index]['weight'];
                }
            }
            
            $completionRate = (($completedMeals + $completedExercises) / max(($totalMeals + $totalExercises), 1)) * 100;
            $avgMood = !empty($moods) ? array_count_values($moods) : [];
            $mostCommonMood = !empty($avgMood) ? array_keys($avgMood, max($avgMood))[0] : 'N/A';
            
            // Build prompt for AI advice
            $prompt = "Bạn là một chuyên gia tư vấn sức khỏe AI. Người dùng vừa hoàn thành TUẦN {$weekNumber} trong kế hoạch sức khỏe của họ.\n\n";
            
            $prompt .= "THÔNG TIN KẾ HOẠCH:\n";
            $prompt .= "- Tiêu đề: {$plan->title}\n";
            $prompt .= "- Tổng số ngày: {$plan->duration_days}\n";
            $prompt .= "- Tuần hiện tại: {$weekNumber} (Ngày {$weekStartDay} - {$weekEndDay})\n";
            $prompt .= "- Tiến độ tổng thể: {$plan->completion_percentage}%\n\n";
            
            if ($profile) {
                $prompt .= "HỒ SƠ SỨC KHỎE:\n";
                if ($profile->age) $prompt .= "- Tuổi: {$profile->age}\n";
                if ($profile->gender) $prompt .= "- Giới tính: {$profile->gender}\n";
                if ($profile->height) $prompt .= "- Chiều cao: {$profile->height} cm\n";
                if ($profile->weight) $prompt .= "- Cân nặng hiện tại: {$profile->weight} kg\n";
                if ($profile->bmi) $prompt .= "- BMI: {$profile->bmi}\n";
                if ($profile->health_goals) {
                    $goals = is_array($profile->health_goals) ? implode(', ', $profile->health_goals) : $profile->health_goals;
                    $prompt .= "- Mục tiêu: {$goals}\n";
                }
                $prompt .= "\n";
            }
            
            $prompt .= "THỐNG KÊ TUẦN {$weekNumber}:\n";
            $prompt .= "- Tổng số bữa ăn: {$totalMeals} (Đã hoàn thành: {$completedMeals})\n";
            $prompt .= "- Tổng số bài tập: {$totalExercises} (Đã hoàn thành: {$completedExercises})\n";
            $prompt .= "- Tỷ lệ hoàn thành: " . round($completionRate, 1) . "%\n";
            $prompt .= "- Tâm trạng phổ biến: {$mostCommonMood}\n";
            if (isset($weekMetrics['weight'])) {
                $prompt .= "- Cân nặng cuối tuần: {$weekMetrics['weight']} kg\n";
            }
            if (isset($weekMetrics['notes'])) {
                $prompt .= "- Ghi chú: {$weekMetrics['notes']}\n";
            }
            $prompt .= "\n";
            
            $prompt .= "YÊU CẦU:\n";
            $prompt .= "1. Đánh giá tổng thể tiến độ của tuần này\n";
            $prompt .= "2. Phân tích điểm mạnh và điểm cần cải thiện\n";
            $prompt .= "3. Đưa ra lời khuyên cụ thể cho tuần tiếp theo\n";
            $prompt .= "4. Khuyến khích và động viên tích cực\n";
            $prompt .= "5. Gợi ý điều chỉnh kế hoạch nếu cần thiết\n";
            $prompt .= "6. Phản hồi bằng TIẾNG VIỆT, chi tiết (300-400 từ)\n";
            $prompt .= "7. Sử dụng dấu đầu dòng và cấu trúc rõ ràng\n";
            
            $systemInstruction = "Bạn là một chuyên gia tư vấn sức khỏe AI chuyên nghiệp. Cung cấp đánh giá toàn diện và lời khuyên thực tế dựa trên tiến độ hàng tuần của người dùng. Luôn phản hồi bằng TIẾNG VIỆT với giọng điệu tích cực và khuyến khích.";
            
            $advice = $geminiService->generateContent(
                $prompt,
                $systemInstruction,
                [],
                [
                    'temperature' => 0.7,
                    'max_tokens' => 800,
                    'timeout' => 60,
                    'http_timeout' => 45,
                    'model' => 'gemini-2.5-flash'
                ]
            );
            
            return $advice;
        } catch (\Exception $e) {
            Log::error('Failed to generate AI advice: ' . $e->getMessage());
            return "Xin chúc mừng! Bạn đã hoàn thành tuần {$weekNumber} thành công. Tiếp tục phát huy tinh thần này!";
        }
    }

    /**
     * Delete a health plan
     */
    public function destroy($id)
    {
        $plan = HealthPlan::where('user_id', Auth::id())->findOrFail($id);
        
        try {
            $planTitle = $plan->title;
            $plan->delete();
            
            return redirect()->route('health-plans.index')
                ->with('success', "Đã xóa kế hoạch '{$planTitle}' thành công.");
        } catch (\Exception $e) {
            Log::error('Failed to delete health plan: ' . $e->getMessage());
            return redirect()->route('health-plans.index')
                ->with('error', 'Không thể xóa kế hoạch. Vui lòng thử lại.');
        }
    }
}

