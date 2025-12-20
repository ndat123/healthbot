<?php

namespace App\Services;

use App\Models\HealthProfile;
use App\Models\NutritionPlan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NutritionService
{
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
        $prompt = "You are a professional nutritionist. Create a personalized {$durationDays}-day nutrition plan.\n\n";
        
        $prompt .= "USER PROFILE:\n";
        if ($profile->age) $prompt .= "- Age: {$profile->age}\n";
        if ($profile->gender) $prompt .= "- Gender: {$profile->gender}\n";
        if ($profile->bmi) $prompt .= "- BMI: {$profile->bmi}\n";
        if ($profile->height && $profile->weight) {
            $prompt .= "- Height: {$profile->height} cm, Weight: {$profile->weight} kg\n";
        }
        
        $prompt .= "- Daily Calorie Target: " . round($dailyCalories) . " kcal\n";
        
        if ($profile->health_goals) {
            $goals = is_array($profile->health_goals) ? implode(', ', $profile->health_goals) : $profile->health_goals;
            $prompt .= "- Health Goals: {$goals}\n";
        }
        
        if ($profile->allergies) {
            $prompt .= "- Allergies: {$profile->allergies}\n";
        }
        
        if ($profile->medical_history) {
            $prompt .= "- Medical History: {$profile->medical_history}\n";
        }
        
        if ($profile->lifestyle_habits) {
            $habits = is_array($profile->lifestyle_habits) ? json_encode($profile->lifestyle_habits) : $profile->lifestyle_habits;
            $prompt .= "- Lifestyle Habits: {$habits}\n";
        }
        
        if (!empty($preferences)) {
            $prompt .= "\nDIETARY PREFERENCES:\n";
            foreach ($preferences as $key => $value) {
                $prompt .= "- " . ucfirst($key) . ": {$value}\n";
            }
        }
        
        // Add reference from existing health plans if available
        if ($existingHealthPlans && count($existingHealthPlans) > 0) {
            $prompt .= "\nREFERENCE FROM PREVIOUS HEALTH PLANS:\n";
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
                    $prompt .= "User has previously eaten: " . implode(', ', array_slice($sampleMeals, 0, 5)) . "...\n";
                    $prompt .= "You can reference and create variations from these meals.\n";
                }
            }
        }
        
        $prompt .= "\nREQUIREMENTS:\n";
        $prompt .= "1. Create EXACTLY {$durationDays} days, each day DIFFERENT (breakfast, lunch, dinner, snack)\n";
        $prompt .= "2. SHORT meal names (e.g., 'Grilled chicken w/ veggies'), portion, calories, protein, carbs, fats\n";
        $prompt .= "3. Total calories per day ~" . round($dailyCalories) . " kcal\n";
        $prompt .= "4. Shopping list & tips VERY SHORT (5-7 items)\n";
        $prompt .= "5. Write ALL content in ENGLISH, CONCISE, NO verbose text\n\n";
        
        $prompt .= "JSON format (CONCISE):\n";
        $prompt .= '{
  "daily_meals": [
    {"day": 1, "meals": [{"time": "Breakfast", "food": "Oatmeal banana", "portion": "250g", "calories": 300, "protein": 10, "carbs": 45, "fats": 8}, {"time": "Lunch", ...}, {"time": "Dinner", ...}, {"time": "Snack", ...}], "nutrition": {"total_calories": 1300}, "shopping_list": ["Oatmeal", "Banana", "Chicken"]},
    {"day": 2, "meals": [...], ...}
  ],
  "tips": ["Meal prep ahead", "Drink water"]
}';
        
        return $prompt;
    }

    /**
     * Get AI response - BẮT BUỘC DÙNG AI
     */
    private function getAIResponse(string $prompt): string
    {
        $apiKey = env('OPENAI_API_KEY');
        
        if (!$apiKey) {
            throw new \RuntimeException('OPENAI_API_KEY chưa được cấu hình trong file .env.');
        }

        try {
            set_time_limit(180); // 3 phút cho PHP script
            
            $response = Http::timeout(150) // 2.5 phút cho HTTP request
                ->withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional nutritionist. Create EXTREMELY CONCISE meal plans. EACH DAY DIFFERENT. Return valid JSON, NO explanation. Write ALL content in ENGLISH.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.6,
                    'max_tokens' => 6000, // Giảm xuống để nhanh hơn nhưng vẫn đủ 14 ngày
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? null;
                if (!$content) {
                    throw new \RuntimeException('OpenAI trả về phản hồi trống');
                }
                Log::info('OpenAI Nutrition API successful');
                return $content;
            } else {
                $error = $response->json()['error']['message'] ?? 'Unknown API error';
                throw new \RuntimeException('Lỗi OpenAI API: ' . $error);
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Nutrition API Error: ' . $e->getMessage());
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
        Log::info('Raw AI Nutrition Response', ['response' => substr($aiResponse, 0, 500)]);
        
        // Bỏ markdown code fence
        $aiResponse = preg_replace('/```json\s*/s', '', $aiResponse);
        $aiResponse = preg_replace('/```\s*$/s', '', $aiResponse);
        $aiResponse = trim($aiResponse);
        
        $decoded = json_decode($aiResponse, true);
        
        if (!$decoded) {
            preg_match('/\{.*\}/s', $aiResponse, $matches);
            if (!empty($matches[0])) {
                $decoded = json_decode($matches[0], true);
            }
        }
        
        if (!$decoded || !isset($decoded['daily_meals']) || !is_array($decoded['daily_meals'])) {
            Log::error('AI nutrition response is invalid', [
                'ai_response' => $aiResponse,
                'decoded' => $decoded,
            ]);
            throw new \RuntimeException('Dữ liệu AI dinh dưỡng không hợp lệ. Vui lòng thử lại.');
        }

        // Kiểm tra từng ngày
        foreach ($decoded['daily_meals'] as $dayMeal) {
            if (!isset($dayMeal['meals']) || !is_array($dayMeal['meals'])) {
                Log::error('Day meal missing required fields', ['day_meal' => $dayMeal]);
                throw new \RuntimeException('Kế hoạch dinh dưỡng thiếu thông tin bắt buộc.');
            }
        }

        Log::info('AI nutrition plan parsed successfully', ['days_count' => count($decoded['daily_meals'])]);
        
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

