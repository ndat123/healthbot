<?php

namespace App\Services;

use App\Models\HealthProfile;
use App\Models\HealthPlan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HealthPlanService
{
    /**
     * Generate health plan using rule-based logic and AI
     */
    public function generatePlan(HealthProfile $profile, int $durationDays = 7): array
    {
        // Step 1: Rule-based analysis
        $rules = $this->applyRules($profile);
        
        // Step 2: Build AI prompt
        $prompt = $this->buildPrompt($profile, $rules, $durationDays);
        
        // Step 3: Get AI response (or use mock for development)
        $aiResponse = $this->getAIResponse($prompt);
        
        // Step 4: Parse and structure the plan
        $planData = $this->parsePlanData($aiResponse, $rules, $durationDays);
        
        return [
            'plan_data' => $planData,
            'ai_prompt' => $prompt,
            'ai_response' => $aiResponse,
            'rules_applied' => $rules,
        ];
    }

    /**
     * Apply rule-based logic
     */
    private function applyRules(HealthProfile $profile): array
    {
        $rules = [];

        // BMI rules
        if ($profile->bmi) {
            if ($profile->bmi > 25) {
                $rules['priority'] = 'weight_loss';
                $rules['recommendations'][] = 'Focus on calorie deficit diet';
                $rules['recommendations'][] = 'Include cardio exercises';
            } elseif ($profile->bmi < 18.5) {
                $rules['priority'] = 'weight_gain';
                $rules['recommendations'][] = 'Focus on calorie surplus diet';
                $rules['recommendations'][] = 'Include strength training';
            } else {
                $rules['priority'] = 'maintenance';
            }
        }

        // Diabetes rules
        if ($profile->blood_sugar && $profile->blood_sugar > 100) {
            $rules['conditions'][] = 'diabetes_risk';
            $rules['restrictions'][] = 'Limit sugar intake';
            $rules['restrictions'][] = 'Monitor blood sugar regularly';
        }

        // High blood pressure rules
        if ($profile->blood_pressure_systolic && $profile->blood_pressure_systolic > 140) {
            $rules['conditions'][] = 'hypertension_risk';
            $rules['restrictions'][] = 'Reduce sodium intake';
            $rules['restrictions'][] = 'Include stress management';
        }

        // Medical history rules
        if ($profile->medical_history) {
            $history = strtolower($profile->medical_history);
            if (strpos($history, 'heart') !== false || strpos($history, 'cardiac') !== false) {
                $rules['conditions'][] = 'heart_condition';
                $rules['restrictions'][] = 'Avoid high-intensity exercises';
            }
        }

        // Allergies rules
        if ($profile->allergies) {
            $rules['allergies'] = explode(',', $profile->allergies);
            $rules['restrictions'][] = 'Avoid allergens in diet';
        }

        // Goals rules
        if ($profile->health_goals) {
            $goals = is_array($profile->health_goals) ? $profile->health_goals : json_decode($profile->health_goals, true);
            if ($goals) {
                $rules['goals'] = $goals;
            }
        }

        return $rules;
    }

    /**
     * Build AI prompt
     */
    private function buildPrompt(HealthProfile $profile, array $rules, int $durationDays): string
    {
        $prompt = "You are a professional health and nutrition coach. Based on the health profile and analysis below, create a personalized {$durationDays}-day health plan.\n\n";

        $prompt .= "USER PROFILE:\n";
        $prompt .= "- Age: " . ($profile->age ?? 'Not specified') . "\n";
        $prompt .= "- Gender: " . ($profile->gender ?? 'Not specified') . "\n";
        $prompt .= "- BMI: " . ($profile->bmi ?? 'Not calculated') . "\n";
        
        if ($profile->height && $profile->weight) {
            $prompt .= "- Height: {$profile->height} cm, Weight: {$profile->weight} kg\n";
        }
        
        if ($profile->medical_history) {
            $prompt .= "- Medical History: {$profile->medical_history}\n";
        }
        
        if ($profile->allergies) {
            $prompt .= "- Allergies: {$profile->allergies}\n";
        }
        
        if ($profile->lifestyle_habits) {
            $habits = is_array($profile->lifestyle_habits) 
                ? $profile->lifestyle_habits 
                : json_decode($profile->lifestyle_habits, true);
            if ($habits) {
                $prompt .= "- Lifestyle Habits: " . json_encode($habits) . "\n";
            }
        }
        
        if ($profile->blood_pressure_systolic) {
            $prompt .= "- Blood Pressure: {$profile->blood_pressure_systolic}/{$profile->blood_pressure_diastolic} mmHg\n";
        }
        
        if ($profile->blood_sugar) {
            $prompt .= "- Blood Sugar: {$profile->blood_sugar} mg/dL\n";
        }

        $prompt .= "\nANALYSIS & RECOMMENDATIONS:\n";
        $prompt .= json_encode($rules, JSON_PRETTY_PRINT) . "\n\n";

        $prompt .= "REQUIREMENTS:\n";
        $prompt .= "1. Create a {$durationDays}-day plan, EACH DAY MUST BE DIFFERENT (meals, exercises, lifestyle tips)\n";
        $prompt .= "2. Write ALL content in clear, easy-to-understand ENGLISH\n";
        $prompt .= "3. Personalize based on health profile, medical history, allergies, and goals\n";
        $prompt .= "4. Realistic portions and safe exercises for the user\n\n";

        $prompt .= "RETURN JSON IN THIS EXACT STRUCTURE (NO EXTRA EXPLANATION):\n";
        $prompt .= '```json
{
  "daily_plans": [
    {
      "day": 1,
      "meals": [
        {"time": "Breakfast", "food": "Oatmeal with unsweetened milk and banana", "calories": 300},
        {"time": "Lunch", "food": "Grilled chicken breast, steamed vegetables, brown rice", "calories": 450},
        {"time": "Dinner", "food": "Pan-seared salmon, green salad", "calories": 400}
      ],
      "exercises": [
        {"type": "Cardio", "name": "Brisk walking", "duration": 30}
      ],
      "lifestyle": ["Drink 8 glasses of water", "Sleep 7-8 hours"],
      "notes": "First day, get familiar with healthy eating habits."
    }
  ],
  "overall_recommendations": ["Overall recommendation 1", "Recommendation 2"],
  "milestones": [
    {"day": 3, "goal": "Day 3 goal"},
    {"day": 7, "goal": "Complete first week"}
  ]
}
```';

        return $prompt;
    }

    /**
     * Get AI response (using OpenAI API - REQUIRED)
     */
    private function getAIResponse(string $prompt): string
    {
        // Check if OpenAI API key is configured
        $apiKey = env('OPENAI_API_KEY');
        
        if (!$apiKey) {
            throw new \RuntimeException('OPENAI_API_KEY chưa được cấu hình trong file .env. Vui lòng thêm API key của bạn để sử dụng tính năng này.');
        }

        try {
            // Tăng timeout PHP script để đợi OpenAI
            set_time_limit(120); // 2 phút
            
            $response = Http::timeout(90) // Timeout request 90s
                ->withOptions(['verify' => false]) // Tắt verify SSL cho development
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional health and nutrition expert. Create highly personalized health plans based on user profiles. EACH DAY MUST BE DIFFERENT. Return valid JSON in the EXACT format requested, NO markdown, NO explanation. Write ALL content in ENGLISH.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 4000,
                    'response_format' => ['type' => 'json_object'], // Bắt buộc trả JSON
                ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? null;
                if (!$content) {
                    throw new \RuntimeException('OpenAI trả về phản hồi trống');
                }
                Log::info('OpenAI API successful response received');
                return $content;
            } else {
                $error = $response->json()['error']['message'] ?? 'Unknown API error';
                throw new \RuntimeException('Lỗi OpenAI API: ' . $error);
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            throw new \RuntimeException('Không thể tạo kế hoạch từ AI: ' . $e->getMessage());
        }
    }


    /**
     * Parse AI response and structure plan data
     */
    private function parsePlanData(string $aiResponse, array $rules, int $durationDays): array
    {
        Log::info('Raw AI Response', ['response' => substr($aiResponse, 0, 500)]);
        
        // Bỏ markdown code fence nếu có (```json ... ```)
        $aiResponse = preg_replace('/```json\s*/s', '', $aiResponse);
        $aiResponse = preg_replace('/```\s*$/s', '', $aiResponse);
        $aiResponse = trim($aiResponse);
        
        // Parse JSON
        $decoded = json_decode($aiResponse, true);

        // Nếu không parse được, thử tìm JSON trong text
        if (!$decoded) {
            preg_match('/\{.*\}/s', $aiResponse, $matches);
            if (!empty($matches[0])) {
                $decoded = json_decode($matches[0], true);
            }
        }

        // Kiểm tra cấu trúc
        if (!$decoded || !isset($decoded['daily_plans']) || !is_array($decoded['daily_plans'])) {
            Log::error('AI health plan response is invalid', [
                'ai_response' => $aiResponse,
                'decoded' => $decoded,
            ]);
            throw new \RuntimeException('Dữ liệu AI không hợp lệ. Vui lòng thử lại.');
        }

        // Kiểm tra từng ngày có đủ trường không
        foreach ($decoded['daily_plans'] as $dayPlan) {
            if (!isset($dayPlan['meals']) || !isset($dayPlan['exercises']) || !isset($dayPlan['lifestyle'])) {
                Log::error('Day plan missing required fields', ['day_plan' => $dayPlan]);
                throw new \RuntimeException('Kế hoạch ngày thiếu thông tin bắt buộc (meals, exercises, lifestyle).');
            }
        }

        Log::info('AI plan parsed successfully', ['days_count' => count($decoded['daily_plans'])]);
        
        return $decoded;
    }
}

