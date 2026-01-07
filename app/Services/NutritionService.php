<?php

namespace App\Services;

use App\Models\HealthProfile;
use App\Models\NutritionPlan;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NutritionService
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Generate personalized nutrition plan
     */
    public function generateNutritionPlan(HealthProfile $profile, array $preferences = [], int $durationDays = 7, $existingHealthPlans = null): array
    {
        // Calculate daily calories
        $dailyCalories = $this->calculateDailyCalories($profile, $preferences);
        
        // Build AI prompt for nutrition (có thể tham khảo health plans cũ)
        $prompt = $this->buildNutritionPrompt($profile, $preferences, $durationDays, $dailyCalories, $existingHealthPlans);
        
        // Get AI response
        $aiResponse = $this->getAIResponse($prompt);
        
        // Parse and structure the plan
        $planData = $this->parseNutritionPlan($aiResponse, $durationDays);
        
        return [
            'plan_data' => $planData,
            'daily_calories' => $dailyCalories,
            'ai_prompt' => $prompt,
            'ai_response' => $aiResponse,
        ];
    }

    /**
     * Build nutrition-specific AI prompt
     */
    private function buildNutritionPrompt(HealthProfile $profile, array $preferences, int $durationDays, float $dailyCalories, $existingHealthPlans = null): string
    {
        $prompt = "Bạn là một chuyên gia dinh dưỡng chuyên nghiệp. Tạo một kế hoạch dinh dưỡng cá nhân hóa {$durationDays} ngày.\n\n";
        
        $prompt .= "HỒ SƠ NGƯỜI DÙNG:\n";
        if ($profile->age) $prompt .= "- Tuổi: {$profile->age}\n";
        if ($profile->gender) $prompt .= "- Giới tính: {$profile->gender}\n";
        if ($profile->bmi) $prompt .= "- BMI: {$profile->bmi}\n";
        if ($profile->height && $profile->weight) {
            $prompt .= "- Chiều cao: {$profile->height} cm, Cân nặng: {$profile->weight} kg\n";
        }
        
        $prompt .= "- Mục tiêu calo hàng ngày: " . round($dailyCalories) . " kcal\n";
        
        if ($profile->health_goals) {
            $goals = is_array($profile->health_goals) ? implode(', ', $profile->health_goals) : $profile->health_goals;
            $prompt .= "- Mục tiêu sức khỏe: {$goals}\n";
        }
        
        if ($profile->allergies) {
            $prompt .= "- Dị ứng: {$profile->allergies}\n";
        }
        
        if ($profile->medical_history) {
            $prompt .= "- Tiền sử bệnh: {$profile->medical_history}\n";
        }
        
        if ($profile->lifestyle_habits) {
            $habits = is_array($profile->lifestyle_habits) ? json_encode($profile->lifestyle_habits) : $profile->lifestyle_habits;
            $prompt .= "- Thói quen lối sống: {$habits}\n";
        }
        
        if (!empty($preferences)) {
            $prompt .= "\nSỞ THÍCH ĂN UỐNG:\n";
            foreach ($preferences as $key => $value) {
                $prompt .= "- " . ucfirst($key) . ": {$value}\n";
            }
        }
        
        // Add reference from existing health plans if available
        if ($existingHealthPlans && count($existingHealthPlans) > 0) {
            $prompt .= "\nTHAM KHẢO TỪ KẾ HOẠCH SỨC KHỎE TRƯỚC ĐÓ:\n";
            $latestPlan = $existingHealthPlans[0];
            if (isset($latestPlan->plan_data['daily_plans'])) {
                $sampleMeals = [];
                foreach (array_slice($latestPlan->plan_data['daily_plans'], 0, 2) as $day) {
                    if (isset($day['meals'])) {
                        foreach ($day['meals'] as $meal) {
                            $sampleMeals[] = $meal['food'] ?? '';
                        }
                    }
                }
                if (!empty($sampleMeals)) {
                    $prompt .= "Người dùng đã từng ăn: " . implode(', ', array_slice($sampleMeals, 0, 5)) . "...\n";
                    $prompt .= "Bạn có thể tham khảo và tạo các biến thể từ những bữa ăn này.\n";
                }
            }
        }
        
        $prompt .= "\nYÊU CẦU:\n";
        $prompt .= "1. Tạo CHÍNH XÁC {$durationDays} ngày, mỗi ngày KHÁC NHAU (bữa sáng, bữa trưa, bữa tối, đồ ăn nhẹ)\n";
        $prompt .= "2. MỖI BỮA ĂN phải có 2-3 LỰA CHỌN với calories chi tiết\n";
        $prompt .= "3. Tên món ăn NGẮN GỌN (ví dụ: 'Gà nướng với rau'), calories, protein, carbs, fat\n";
        $prompt .= "4. Tổng calo mỗi ngày ~" . round($dailyCalories) . " kcal\n";
        $prompt .= "5. Danh sách mua sắm & mẹo RẤT NGẮN (5-7 mục)\n";
        $prompt .= "6. Viết TẤT CẢ nội dung bằng TIẾNG VIỆT, NGẮN GỌN, KHÔNG dài dòng\n\n";
        
        $prompt .= "Định dạng JSON (NGẮN GỌN):\n";
        $prompt .= '{
  "daily_meals": [
    {
      "day": 1, 
      "meals": [
        {
          "time": "Bữa sáng",
          "options": [
            {"food": "Cháo yến mạch chuối", "calories": 300, "protein": 10, "carbs": 45, "fat": 8},
            {"food": "Bánh mì trứng", "calories": 320, "protein": 12, "carbs": 42, "fat": 10}
          ]
        },
        {
          "time": "Bữa trưa",
          "options": [
            {"food": "Cơm gà nướng rau", "calories": 500, "protein": 30, "carbs": 60, "fat": 12},
            {"food": "Phở bò", "calories": 480, "protein": 28, "carbs": 58, "fat": 11}
          ]
        },
        {
          "time": "Bữa tối",
          "options": [
            {"food": "Cá hấp rau củ", "calories": 400, "protein": 25, "carbs": 50, "fat": 10},
            {"food": "Canh chua cá", "calories": 380, "protein": 23, "carbs": 48, "fat": 9}
          ]
        }
      ], 
      "nutrition": {"total_calories": 1300, "protein": 65, "carbs": 155, "fat": 30}, 
      "shopping_list": ["Yến mạch", "Chuối", "Gà", "Rau"]
    },
    {"day": 2, "meals": [...], ...}
  ],
  "tips": ["Chuẩn bị bữa ăn trước", "Uống nước"]
}';
        
        return $prompt;
    }

    /**
     * Get AI response - BẮT BUỘC DÙNG AI
     */
    private function getAIResponse(string $prompt): string
    {
        try {
            $systemInstruction = 'Bạn là một chuyên gia dinh dưỡng chuyên nghiệp. Tạo các kế hoạch bữa ăn CỰC KỲ NGẮN GỌN. MỖI NGÀY KHÁC NHAU. Trả về JSON hợp lệ, KHÔNG giải thích. Viết TẤT CẢ nội dung bằng TIẾNG VIỆT.';
            
            $content = $this->geminiService->generateJsonContent(
                $prompt,
                $systemInstruction,
                [],
                [
                    'temperature' => 0.6,
                    'max_tokens' => 16384, // Increase for full plan with multiple options per meal
                    'timeout' => 180,
                    'http_timeout' => 150,
                    'model' => 'gemini-2.5-flash'
                ]
            );
            
            Log::info('Gemini Nutrition API successful');
            return $content;
        } catch (\Exception $e) {
            Log::error('Gemini Nutrition API Error: ' . $e->getMessage());
            throw new \RuntimeException('Không thể tạo kế hoạch dinh dưỡng từ AI: ' . $e->getMessage());
        }
    }

    /**
     * Mock nutrition response
     */
    private function getMockNutritionResponse(): string
    {
        return json_encode([
            'daily_meals' => [
                [
                    'day' => 1,
                    'meals' => [
                        [
                            'time' => 'Breakfast',
                            'food' => 'Oatmeal with berries and nuts',
                            'portion' => '1 cup cooked oatmeal, 1/2 cup berries, 1 oz almonds',
                            'calories' => 350,
                            'protein' => 12,
                            'carbs' => 45,
                            'fats' => 15
                        ],
                        [
                            'time' => 'Lunch',
                            'food' => 'Grilled chicken salad',
                            'portion' => '4 oz chicken, mixed greens, vegetables, olive oil dressing',
                            'calories' => 450,
                            'protein' => 35,
                            'carbs' => 20,
                            'fats' => 25
                        ],
                        [
                            'time' => 'Dinner',
                            'food' => 'Salmon with quinoa and vegetables',
                            'portion' => '5 oz salmon, 1 cup quinoa, steamed vegetables',
                            'calories' => 550,
                            'protein' => 40,
                            'carbs' => 50,
                            'fats' => 20
                        ],
                        [
                            'time' => 'Snack',
                            'food' => 'Greek yogurt with fruit',
                            'portion' => '1 cup Greek yogurt, 1/2 cup mixed berries',
                            'calories' => 200,
                            'protein' => 15,
                            'carbs' => 25,
                            'fats' => 5
                        ]
                    ],
                    'nutrition' => [
                        'total_calories' => 1550,
                        'protein' => 102,
                        'carbs' => 140,
                        'fats' => 65,
                        'fiber' => 25,
                        'vitamins' => 'Rich in Vitamin D, Omega-3, Antioxidants'
                    ],
                    'shopping_list' => [
                        'Oatmeal', 'Berries', 'Almonds', 'Chicken breast', 'Salmon',
                        'Quinoa', 'Mixed greens', 'Greek yogurt', 'Vegetables'
                    ]
                ]
            ],
            'tips' => [
                'Meal prep on Sundays for the week',
                'Stay hydrated - drink water before meals',
                'Include protein in every meal',
                'Eat a variety of colorful vegetables'
            ]
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Parse nutrition plan from AI response
     */
    private function parseNutritionPlan(string $aiResponse, int $durationDays): array
    {
        Log::info('Raw AI Nutrition Response', ['response_length' => strlen($aiResponse), 'preview' => substr($aiResponse, 0, 500)]);
        
        // Bỏ markdown code fence
        $aiResponse = preg_replace('/```json\s*/s', '', $aiResponse);
        $aiResponse = preg_replace('/```\s*$/s', '', $aiResponse);
        $aiResponse = trim($aiResponse);
        
        // Remove control characters that might cause JSON parsing errors
        // Keep only printable characters and whitespace
        $aiResponse = preg_replace('/[\x00-\x1F\x7F]/u', '', $aiResponse);
        
        // Try to fix incomplete JSON (if truncated)
        $decoded = json_decode($aiResponse, true);
        
        if (!$decoded) {
            // Try to extract JSON from response (find the first { and last })
            preg_match('/\{.*\}/s', $aiResponse, $matches);
            if (!empty($matches[0])) {
                $decoded = json_decode($matches[0], true);
            }
        }
        
        // If still invalid, try to fix incomplete JSON by adding closing brackets
        if (!$decoded && substr($aiResponse, -1) !== '}') {
            // Find the last complete object/array position
            $lastCompletePos = strrpos($aiResponse, '}');
            if ($lastCompletePos === false) {
                $lastCompletePos = strrpos($aiResponse, ']');
            }
            
            if ($lastCompletePos !== false) {
                // Try to extract up to last complete position and add closing brackets
                $partialResponse = substr($aiResponse, 0, $lastCompletePos + 1);
                
                // Count open/close brackets
                $openBraces = substr_count($partialResponse, '{');
                $closeBraces = substr_count($partialResponse, '}');
                $openBrackets = substr_count($partialResponse, '[');
                $closeBrackets = substr_count($partialResponse, ']');
                
                // Try to complete the JSON
                $fixedResponse = $partialResponse;
                while ($openBrackets > $closeBrackets) {
                    $fixedResponse .= ']';
                    $closeBrackets++;
                }
                while ($openBraces > $closeBraces) {
                    $fixedResponse .= '}';
                    $closeBraces++;
                }
                
                $decoded = json_decode($fixedResponse, true);
                if ($decoded) {
                    Log::warning('Fixed incomplete JSON response', [
                        'original_length' => strlen($aiResponse),
                        'fixed_length' => strlen($fixedResponse),
                        'days_recovered' => isset($decoded['daily_meals']) ? count($decoded['daily_meals']) : 0
                    ]);
                }
            }
        }
        
        if (!$decoded || !isset($decoded['daily_meals']) || !is_array($decoded['daily_meals'])) {
            Log::error('AI nutrition response is invalid', [
                'ai_response_length' => strlen($aiResponse),
                'ai_response_preview' => substr($aiResponse, 0, 1000),
                'decoded' => $decoded,
                'json_error' => json_last_error_msg(),
            ]);
            throw new \RuntimeException('Dữ liệu AI dinh dưỡng không hợp lệ. Vui lòng thử lại.');
        }

        // Validate number of days
        $receivedDays = count($decoded['daily_meals']);
        if ($receivedDays < $durationDays) {
            Log::warning('AI returned fewer days than requested', [
                'requested' => $durationDays,
                'received' => $receivedDays
            ]);
            // Continue with what we have, but log warning
        }

        // Kiểm tra từng ngày
        foreach ($decoded['daily_meals'] as $index => $dayMeal) {
            if (!isset($dayMeal['meals']) || !is_array($dayMeal['meals'])) {
                Log::error('Day meal missing required fields', ['day_index' => $index, 'day_meal' => $dayMeal]);
                throw new \RuntimeException('Kế hoạch dinh dưỡng thiếu thông tin bắt buộc.');
            }
            
            // Validate meals structure (should have options or food field)
            foreach ($dayMeal['meals'] as $mealIndex => $meal) {
                if (!isset($meal['time'])) {
                    Log::warning('Meal missing time field', ['day_index' => $index, 'meal_index' => $mealIndex]);
                }
                // Support both formats: with options or single food
                if (!isset($meal['options']) && !isset($meal['food'])) {
                    Log::warning('Meal missing both options and food', ['day_index' => $index, 'meal_index' => $mealIndex]);
                }
            }
        }

        Log::info('AI nutrition plan parsed successfully', [
            'days_count' => count($decoded['daily_meals']),
            'requested_days' => $durationDays
        ]);
        
        return $decoded;
    }

    /**
     * Create basic nutrition plan
     */
    private function createBasicNutritionPlan(int $durationDays): array
    {
        $meals = [];
        for ($i = 1; $i <= $durationDays; $i++) {
            $meals[] = [
                'day' => $i,
                'meals' => [
                    ['time' => 'Breakfast', 'food' => 'Balanced breakfast', 'calories' => 350],
                    ['time' => 'Lunch', 'food' => 'Balanced lunch', 'calories' => 450],
                    ['time' => 'Dinner', 'food' => 'Balanced dinner', 'calories' => 500],
                    ['time' => 'Snack', 'food' => 'Healthy snack', 'calories' => 200],
                ],
                'nutrition' => ['total_calories' => 1500],
                'shopping_list' => []
            ];
        }

        return [
            'daily_meals' => $meals,
            'tips' => ['Follow the meal plan consistently', 'Stay hydrated']
        ];
    }

    /**
     * Extend nutrition plan
     */
    private function extendNutritionPlan(array $plan, int $durationDays): array
    {
        $existingMeals = $plan['daily_meals'];
        $lastMeal = end($existingMeals);
        
        for ($i = count($existingMeals) + 1; $i <= $durationDays; $i++) {
            $plan['daily_meals'][] = array_merge($lastMeal, ['day' => $i]);
        }

        return $plan;
    }

    /**
     * Calculate daily calories based on profile
     */
    private function calculateDailyCalories(HealthProfile $profile, array $preferences): float
    {
        // Basic BMR calculation (Mifflin-St Jeor Equation)
        if ($profile->age && $profile->height && $profile->weight) {
            $bmr = 0;
            if ($profile->gender === 'male') {
                $bmr = 10 * $profile->weight + 6.25 * $profile->height - 5 * $profile->age + 5;
            } elseif ($profile->gender === 'female') {
                $bmr = 10 * $profile->weight + 6.25 * $profile->height - 5 * $profile->age - 161;
            }
            
            // Activity multiplier (assuming moderate activity)
            $activityMultiplier = 1.55;
            $dailyCalories = $bmr * $activityMultiplier;
            
            // Adjust based on goals
            if ($profile->health_goals) {
                $goals = is_array($profile->health_goals) ? $profile->health_goals : [$profile->health_goals];
                if (in_array('weight_loss', $goals)) {
                    $dailyCalories -= 500; // Calorie deficit
                } elseif (in_array('muscle_gain', $goals)) {
                    $dailyCalories += 300; // Calorie surplus
                }
            }
            
            return max(1200, round($dailyCalories, 2)); // Minimum 1200 calories
        }

        return 2000; // Default
    }
}

