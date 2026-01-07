<?php

namespace App\Http\Controllers;

use App\Models\NutritionPlan;
use App\Models\HealthProfile;
use App\Services\NutritionService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NutritionController extends Controller
{
    protected $nutritionService;

    public function __construct(NutritionService $nutritionService)
    {
        $this->nutritionService = $nutritionService;
    }

    /**
     * Show nutrition consultations page
     */
    public function index()
    {
        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();
        $plans = NutritionPlan::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('nutrition.index', compact('profile', 'plans'));
    }

    /**
     * Generate nutrition plan
     */
    public function generatePlan(Request $request)
    {
        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return redirect()->route('health-plans.profile')
                ->with('error', 'Vui lòng tạo hồ sơ sức khỏe trước.');
        }

        $validated = $request->validate([
            'duration_days' => 'required|integer|min:7|max:30',
            'dietary_preferences' => 'nullable|string|max:500',
            'allergies_restrictions' => 'nullable|string|max:500',
        ]);

        $preferences = [];
        if ($validated['dietary_preferences'] ?? null) {
            $preferences['dietary_preferences'] = $validated['dietary_preferences'];
        }
        if ($validated['allergies_restrictions'] ?? null) {
            $preferences['allergies_restrictions'] = $validated['allergies_restrictions'];
        }

        try {
            // Lấy health plans cũ để tham khảo (nếu có)
            $existingHealthPlans = \App\Models\HealthPlan::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->get();

            // Generate plan
            $planData = $this->nutritionService->generateNutritionPlan(
                $profile,
                $preferences,
                $validated['duration_days'],
                $existingHealthPlans
            );
        } catch (\Throwable $e) {
            \Log::error('Failed to generate AI nutrition plan', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể tạo kế hoạch dinh dưỡng từ AI. Vui lòng thử lại sau hoặc kiểm tra API Key.');
        }

        // Create nutrition plan
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays($validated['duration_days'] - 1);

        $plan = NutritionPlan::create([
            'user_id' => $user->id,
            'health_profile_id' => $profile->id,
            'title' => "Kế hoạch dinh dưỡng {$validated['duration_days']} ngày - " . Carbon::now()->format('M Y'),
            'plan_data' => $planData['plan_data'],
            'duration_days' => $validated['duration_days'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'dietary_preferences' => $validated['dietary_preferences'] ?? null,
            'allergies_restrictions' => $validated['allergies_restrictions'] ?? null,
            'daily_calories' => $planData['daily_calories'],
            'ai_prompt_used' => $planData['ai_prompt'],
            'ai_response' => $planData['ai_response'],
        ]);

        return redirect()->route('nutrition.show', $plan->id)
            ->with('success', 'Kế hoạch dinh dưỡng cá nhân hóa của bạn đã được tạo!');
    }

    /**
     * Show nutrition plan details
     */
    public function show($id)
    {
        $plan = NutritionPlan::where('user_id', Auth::id())->findOrFail($id);
        $profile = $plan->healthProfile;

        return view('nutrition.show', compact('plan', 'profile'));
    }

    /**
     * Adjust nutrition plan when user can't follow the schedule
     */
    public function adjustPlan(Request $request, $id)
    {
        $plan = NutritionPlan::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|max:' . $plan->duration_days,
            'reason' => 'required|string|max:500',
            'weight' => 'nullable|numeric|min:0|max:500',
            'mood' => 'nullable|string',
            'actual_meals' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $dayNumber = $validated['day_number'];
        $remainingDays = $plan->duration_days - $dayNumber;
        
        if ($remainingDays > 0) {
            // Use AI to regenerate nutrition plan for remaining days
            $adjustmentSummary = $this->regenerateRemainingDays($plan, $dayNumber, $validated, $remainingDays);
            
            return response()->json([
                'success' => true,
                'adjustment_summary' => $adjustmentSummary,
                'remaining_days' => $remainingDays,
                'message' => 'Kế hoạch dinh dưỡng đã được điều chỉnh thành công!'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'adjustment_summary' => 'Đã lưu thông tin. Đây là ngày cuối cùng của kế hoạch.',
                'remaining_days' => 0,
                'message' => 'Thông tin đã được lưu!'
            ]);
        }
    }

    /**
     * Regenerate nutrition plan for remaining days using AI
     */
    private function regenerateRemainingDays($plan, $fromDay, $adjustmentInfo, $remainingDays)
    {
        try {
            $geminiService = app(GeminiService::class);
            $profile = $plan->healthProfile;
            
            // Build prompt for AI
            $prompt = "Bạn là chuyên gia dinh dưỡng AI. Người dùng đang theo kế hoạch dinh dưỡng nhưng NGÀY {$fromDay} không thể theo được.\n\n";
            
            $prompt .= "THÔNG TIN KẾ HOẠCH GỐC:\n";
            $prompt .= "- Tổng số ngày: {$plan->duration_days}\n";
            $prompt .= "- Ngày hiện tại: {$fromDay}\n";
            $prompt .= "- Số ngày còn lại: {$remainingDays}\n";
            if ($plan->daily_calories) {
                $prompt .= "- Calories mục tiêu/ngày: {$plan->daily_calories} kcal\n";
            }
            if ($plan->dietary_preferences) {
                $prompt .= "- Sở thích ăn uống: {$plan->dietary_preferences}\n";
            }
            if ($plan->allergies_restrictions) {
                $prompt .= "- Dị ứng/Hạn chế: {$plan->allergies_restrictions}\n";
            }
            $prompt .= "\n";
            
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
            if ($adjustmentInfo['weight']) {
                $prompt .= "- Cân nặng: {$adjustmentInfo['weight']}kg\n";
            }
            if ($adjustmentInfo['mood']) {
                $prompt .= "- Tâm trạng: {$adjustmentInfo['mood']}\n";
            }
            
            // AI only generates plans for days AFTER the adjustment day
            $totalDaysToGenerate = $remainingDays;
            $nextDay = $fromDay + 1;
            
            $prompt .= "\nYÊU CẦU QUAN TRỌNG:\n";
            $prompt .= "1. PHẢI TẠO ĐỦ {$totalDaysToGenerate} NGÀY (từ ngày {$nextDay} đến ngày {$plan->duration_days})\n";
            $prompt .= "2. Điều chỉnh dựa trên tình hình ngày {$fromDay}: {$adjustmentInfo['reason']}\n";
            $prompt .= "3. Mỗi bữa ăn có 2-3 lựa chọn với calories chi tiết\n";
            $prompt .= "4. Bao gồm thông tin dinh dưỡng (protein, carbs, fat)\n";
            $prompt .= "5. TIẾNG VIỆT ngắn gọn\n";
            $prompt .= "6. Trả về JSON thuần (không markdown)\n\n";
            
            $prompt .= 'CẤU TRÚC JSON BẮT BUỘC (phải có ĐỦ ' . $totalDaysToGenerate . ' ngày từ ngày ' . $nextDay . ' đến ' . $plan->duration_days . '):\n{
  "daily_meals": [';
            
            // Generate example structure for remaining days
            for ($d = $nextDay; $d <= $plan->duration_days; $d++) {
                if ($d > $nextDay) $prompt .= ',';
                $prompt .= '
    {
      "day": ' . $d . ',
      "meals": [
        {
          "time": "Bữa sáng",
          "options": [
            {"food": "Lựa chọn 1", "calories": 400, "protein": 20, "carbs": 50, "fat": 10},
            {"food": "Lựa chọn 2", "calories": 420, "protein": 22, "carbs": 52, "fat": 11}
          ]
        },
        {
          "time": "Bữa trưa",
          "options": [
            {"food": "Lựa chọn 1", "calories": 600, "protein": 35, "carbs": 70, "fat": 15},
            {"food": "Lựa chọn 2", "calories": 620, "protein": 37, "carbs": 72, "fat": 16}
          ]
        },
        {
          "time": "Bữa tối",
          "options": [
            {"food": "Lựa chọn 1", "calories": 500, "protein": 30, "carbs": 60, "fat": 12},
            {"food": "Lựa chọn 2", "calories": 520, "protein": 32, "carbs": 62, "fat": 13}
          ]
        }
      ],
      "nutrition": {
        "total_calories": 1500,
        "protein": 85,
        "carbs": 180,
        "fat": 37
      },
      "notes": "Ghi chú ngắn"
    }';
            }
            
            $prompt .= '
  ],
  "adjustment_summary": "Tóm tắt điều chỉnh"
}';
            
            $systemInstruction = "Chuyên gia dinh dưỡng AI. QUAN TRỌNG: Phải tạo CHÍNH XÁC {$totalDaysToGenerate} ngày trong daily_meals array, từ ngày {$nextDay} đến ngày {$plan->duration_days}. Trả JSON thuần không markdown. TIẾNG VIỆT.";
            
            Log::info('Requesting AI to adjust nutrition plan', [
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
            
            Log::info('AI nutrition adjustment response received', ['response_length' => strlen($response)]);
            
            // Parse response
            $response = preg_replace('/```json\s*/s', '', $response);
            $response = preg_replace('/```\s*$/s', '', $response);
            $response = trim($response);
            
            $decoded = json_decode($response, true);
            
            if (!$decoded || !isset($decoded['daily_meals']) || !is_array($decoded['daily_meals'])) {
                Log::error('AI response missing daily_meals', ['decoded' => $decoded]);
                throw new \RuntimeException('AI response missing daily_meals');
            }
            
            // Validate number of days returned
            $receivedDaysCount = count($decoded['daily_meals']);
            if ($receivedDaysCount < $totalDaysToGenerate) {
                Log::warning('AI returned fewer days than expected', [
                    'expected' => $totalDaysToGenerate,
                    'received' => $receivedDaysCount
                ]);
            }
            
            // Create adjustment day plan from actual data
            $planData = $plan->plan_data;
            $adjustmentDayIdx = $fromDay - 1;
            $adjustmentDayPlan = $this->createAdjustmentDayPlan($fromDay, $adjustmentInfo, $plan);
            $planData['daily_meals'][$adjustmentDayIdx] = $adjustmentDayPlan;
            
            Log::info('Created adjustment day nutrition plan from actual data', [
                'day' => $fromDay,
                'index' => $adjustmentDayIdx
            ]);
            
            // Update remaining days with AI-generated plans
            Log::info('Updating remaining days with AI nutrition plans', [
                'from_day' => $fromDay + 1,
                'remaining_days' => $remainingDays,
                'new_days_count' => $receivedDaysCount
            ]);
            
            foreach ($decoded['daily_meals'] as $newDayPlan) {
                $dayNumber = $newDayPlan['day'];
                $dayIdx = $dayNumber - 1;
                
                // Update only days AFTER adjustment day
                if ($dayNumber > $fromDay && $dayNumber <= $plan->duration_days) {
                    $planData['daily_meals'][$dayIdx] = $newDayPlan;
                    Log::info('Updated day nutrition plan', ['day' => $dayNumber, 'index' => $dayIdx]);
                }
            }
            
            $plan->plan_data = $planData;
            $plan->save();
            $plan->refresh();
            
            Log::info('Nutrition plan adjustment saved successfully');
            
            return $decoded['adjustment_summary'] ?? "Đã điều chỉnh kế hoạch dinh dưỡng {$remainingDays} ngày còn lại dựa trên tình hình thực tế của bạn.";
            
        } catch (\Exception $e) {
            Log::error('Failed to regenerate nutrition plan: ' . $e->getMessage());
            return "Đã lưu thông tin điều chỉnh. Vui lòng tiếp tục theo kế hoạch hiện tại hoặc tạo kế hoạch mới.";
        }
    }

    /**
     * Create adjustment day nutrition plan based on actual user data using AI analysis
     */
    private function createAdjustmentDayPlan($dayNumber, $adjustmentInfo, $plan)
    {
        try {
            $geminiService = app(GeminiService::class);
            
            $prompt = "Phân tích bữa ăn thực tế và tạo kế hoạch dinh dưỡng cho ngày điều chỉnh.\n\n";
            $prompt .= "THÔNG TIN THỰC TẾ NGÀY {$dayNumber}:\n";
            $prompt .= "- Lý do: {$adjustmentInfo['reason']}\n";
            
            if (!empty($adjustmentInfo['actual_meals'])) {
                $prompt .= "- Bữa ăn thực tế:\n{$adjustmentInfo['actual_meals']}\n";
            }
            
            if (!empty($adjustmentInfo['weight'])) {
                $prompt .= "- Cân nặng: {$adjustmentInfo['weight']}kg\n";
            }
            
            $prompt .= "\nYÊU CẦU:\n";
            $prompt .= "1. Ước tính calories và dinh dưỡng cho mỗi bữa ăn thực tế\n";
            $prompt .= "2. Tạo 1-2 gợi ý thay thế tương đương\n";
            $prompt .= "3. Nếu không có thông tin, tạo gợi ý phù hợp với lý do điều chỉnh\n";
            $prompt .= "4. TIẾNG VIỆT ngắn gọn\n\n";
            
            $prompt .= 'Trả về JSON:\n{
  "day": ' . $dayNumber . ',
  "meals": [
    {
      "time": "Bữa sáng",
      "options": [
        {"food": "Món đã ăn", "calories": 400, "protein": 20, "carbs": 50, "fat": 10},
        {"food": "Gợi ý thay thế", "calories": 400, "protein": 20, "carbs": 50, "fat": 10}
      ]
    }
  ],
  "nutrition": {"total_calories": 1500, "protein": 85, "carbs": 180, "fat": 37},
  "notes": "Ghi chú"
}';
            
            $systemInstruction = "Chuyên gia dinh dưỡng AI. Phân tích bữa ăn thực tế, ước tính calories và dinh dưỡng chính xác. Trả JSON thuần không markdown. TIẾNG VIỆT.";
            
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
            
            $response = preg_replace('/```json\s*/s', '', $response);
            $response = preg_replace('/```\s*$/s', '', $response);
            $response = trim($response);
            
            $decoded = json_decode($response, true);
            
            if ($decoded && isset($decoded['meals'])) {
                Log::info('AI created adjustment day nutrition plan successfully', ['day' => $dayNumber]);
                return $decoded;
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to create adjustment day nutrition plan with AI: ' . $e->getMessage());
        }
        
        // Fallback
        return $this->createBasicAdjustmentDayPlan($dayNumber, $adjustmentInfo, $plan);
    }

    /**
     * Fallback: Create basic adjustment day nutrition plan without AI
     */
    private function createBasicAdjustmentDayPlan($dayNumber, $adjustmentInfo, $plan)
    {
        $dayPlan = [
            'day' => $dayNumber,
            'meals' => [],
            'nutrition' => [
                'total_calories' => 0,
                'protein' => 0,
                'carbs' => 0,
                'fat' => 0
            ],
            'notes' => ''
        ];
        
        // Parse actual meals
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
                
                if (preg_match('/^(Bữa\s+sáng):\s*(.+)$/ui', $line, $matches)) {
                    $mealsData['Bữa sáng'] = trim($matches[2]);
                } elseif (preg_match('/^(Bữa\s+trưa):\s*(.+)$/ui', $line, $matches)) {
                    $mealsData['Bữa trưa'] = trim($matches[2]);
                } elseif (preg_match('/^(Bữa\s+tối):\s*(.+)$/ui', $line, $matches)) {
                    $mealsData['Bữa tối'] = trim($matches[2]);
                }
            }
        }
        
        // Create meals with actual food names
        $totalCalories = 0;
        foreach ($mealsData as $time => $food) {
            $calories = $this->estimateCalories($time);
            $totalCalories += $calories;
            
            $dayPlan['meals'][] = [
                'time' => $time,
                'options' => [
                    [
                        'food' => $food ?: 'Chưa ghi nhận',
                        'calories' => $food ? $calories : 0,
                        'protein' => round($calories * 0.25 / 4),
                        'carbs' => round($calories * 0.50 / 4),
                        'fat' => round($calories * 0.25 / 9)
                    ]
                ]
            ];
        }
        
        $dayPlan['nutrition']['total_calories'] = $totalCalories;
        $dayPlan['nutrition']['protein'] = round($totalCalories * 0.25 / 4);
        $dayPlan['nutrition']['carbs'] = round($totalCalories * 0.50 / 4);
        $dayPlan['nutrition']['fat'] = round($totalCalories * 0.25 / 9);
        
        // Notes
        $notes = ['Ngày điều chỉnh'];
        if (!empty($adjustmentInfo['mood'])) {
            $moodMap = ['excellent' => 'Tuyệt vời', 'good' => 'Tốt', 'okay' => 'Ổn', 'poor' => 'Không tốt'];
            $notes[] = 'Tâm trạng: ' . ($moodMap[$adjustmentInfo['mood']] ?? $adjustmentInfo['mood']);
        }
        if (!empty($adjustmentInfo['weight'])) {
            $notes[] = 'Cân nặng: ' . $adjustmentInfo['weight'] . 'kg';
        }
        
        $dayPlan['notes'] = implode('. ', $notes);
        
        return $dayPlan;
    }

    /**
     * Estimate calories based on meal time
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
     * Update meal completion status
     */
    public function updateMealCompletion(Request $request, $id)
    {
        $plan = NutritionPlan::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'day' => 'required|integer|min:1|max:' . $plan->duration_days,
            'meal_index' => 'required|integer|min:0',
            'meal_key' => 'required|string',
            'completed' => 'required|boolean',
        ]);

        $progressData = $plan->progress_data ?? ['daily_progress' => []];
        $dayIndex = $validated['day'] - 1;
        
        if (!isset($progressData['daily_progress'][$dayIndex])) {
            $progressData['daily_progress'][$dayIndex] = ['day' => $validated['day']];
        }
        
        if (!isset($progressData['daily_progress'][$dayIndex]['meals'])) {
            $progressData['daily_progress'][$dayIndex]['meals'] = [];
        }
        
        $progressData['daily_progress'][$dayIndex]['meals'][$validated['meal_key']] = $validated['completed'];
        
        $plan->progress_data = $progressData;
        $plan->updateCompletionPercentage();
        $plan->save();

        return response()->json([
            'success' => true,
            'completion_percentage' => $plan->completion_percentage
        ]);
    }

    /**
     * Save meal selection (when user chooses from multiple options)
     */
    public function saveMealSelection(Request $request, $id)
    {
        $plan = NutritionPlan::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'meal_key' => 'required|string',
            'option_index' => 'required|integer|min:0',
        ]);

        $progressData = $plan->progress_data ?? [];
        
        if (!isset($progressData['meal_selections'])) {
            $progressData['meal_selections'] = [];
        }
        
        $progressData['meal_selections'][$validated['meal_key']] = $validated['option_index'];
        
        $plan->progress_data = $progressData;
        $plan->save();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a nutrition plan
     */
    public function destroy($id)
    {
        $plan = NutritionPlan::where('user_id', Auth::id())->findOrFail($id);
        
        try {
            $planTitle = $plan->title;
            $plan->delete();
            
            return redirect()->route('nutrition.index')
                ->with('success', "Đã xóa kế hoạch dinh dưỡng '{$planTitle}' thành công.");
        } catch (\Exception $e) {
            Log::error('Failed to delete nutrition plan: ' . $e->getMessage());
            return redirect()->route('nutrition.index')
                ->with('error', 'Không thể xóa kế hoạch dinh dưỡng. Vui lòng thử lại.');
        }
    }
}

