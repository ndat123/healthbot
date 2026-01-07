<?php

namespace App\Services;

use App\Models\HealthProfile;
use App\Models\HealthPlan;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HealthPlanService
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

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
        $prompt = "Tạo kế hoạch sức khỏe {$durationDays} ngày cá nhân hóa.\n\n";

        $prompt .= "HỒ SƠ:\n";
        $prompt .= "- Tuổi: " . ($profile->age ?? 'N/A') . ", Giới tính: " . ($profile->gender ?? 'N/A') . ", BMI: " . ($profile->bmi ?? 'N/A') . "\n";
        
        if ($profile->height && $profile->weight) {
            $prompt .= "- Chiều cao: {$profile->height}cm, Cân nặng: {$profile->weight}kg\n";
        }
        
        if ($profile->medical_history) {
            $prompt .= "- Tiền sử: {$profile->medical_history}\n";
        }
        
        if ($profile->allergies) {
            $prompt .= "- Dị ứng: {$profile->allergies}\n";
        }
        
        if ($profile->lifestyle_habits) {
            $habits = is_array($profile->lifestyle_habits) 
                ? $profile->lifestyle_habits 
                : json_decode($profile->lifestyle_habits, true);
            if ($habits) {
                $habitsSummary = [];
                if (isset($habits['exercise_frequency'])) $habitsSummary[] = "Tập: {$habits['exercise_frequency']}";
                if (isset($habits['sleep_hours'])) $habitsSummary[] = "Ngủ: {$habits['sleep_hours']}h";
                if (isset($habits['smoking']) && $habits['smoking']) $habitsSummary[] = "Hút thuốc";
                if (isset($habits['alcohol'])) $habitsSummary[] = "Rượu: {$habits['alcohol']}";
                if (!empty($habitsSummary)) {
                    $prompt .= "- Thói quen: " . implode(", ", $habitsSummary) . "\n";
                }
            }
        }
        
        if ($profile->blood_pressure_systolic) {
            $prompt .= "- Huyết áp: {$profile->blood_pressure_systolic}/{$profile->blood_pressure_diastolic}\n";
        }
        
        if ($profile->blood_sugar) {
            $prompt .= "- Đường huyết: {$profile->blood_sugar}\n";
        }

        if (!empty($rules)) {
            $prompt .= "\nPHÂN TÍCH:\n";
            if (isset($rules['priority'])) $prompt .= "- Ưu tiên: {$rules['priority']}\n";
            if (isset($rules['goals'])) $prompt .= "- Mục tiêu: " . implode(", ", $rules['goals']) . "\n";
            if (isset($rules['restrictions'])) $prompt .= "- Hạn chế: " . implode("; ", $rules['restrictions']) . "\n";
        }

        $prompt .= "\nYÊU CẦU:\n";
        $prompt .= "1. Tạo {$durationDays} ngày, mỗi ngày khác nhau\n";
        $prompt .= "2. MỖI BỮA ĂN có 2-3 lựa chọn để người dùng chọn\n";
        $prompt .= "3. Mỗi ngày: 3-5 bữa ăn, 1-2 bài tập, 2-3 lối sống\n";
        $prompt .= "4. Viết TIẾNG VIỆT, ngắn gọn, thực tế\n";
        $prompt .= "5. Trả về JSON thuần (không markdown)\n\n";

        $prompt .= "CẤU TRÚC JSON:\n";
        $prompt .= '{
  "daily_plans": [
    {
      "day": 1,
      "meals": [
        {
          "time": "Bữa sáng",
          "options": [
            {"food": "Lựa chọn 1", "calories": 300},
            {"food": "Lựa chọn 2", "calories": 320},
            {"food": "Lựa chọn 3", "calories": 280}
          ]
        },
        {
          "time": "Bữa trưa",
          "options": [
            {"food": "Lựa chọn 1", "calories": 450},
            {"food": "Lựa chọn 2", "calories": 470}
          ]
        }
      ],
      "exercises": [
        {"type": "Cardio", "name": "Tên bài tập", "duration": 30}
      ],
      "lifestyle": ["Mẹo 1", "Mẹo 2"],
      "notes": "Ghi chú ngắn"
    }
  ],
  "overall_recommendations": ["Khuyến nghị 1", "Khuyến nghị 2"],
  "milestones": [
    {"day": 3, "goal": "Mục tiêu ngắn"},
    {"day": 7, "goal": "Mục tiêu ngắn"}
  ]
}';

        return $prompt;
    }

    /**
     * Get AI response (using Gemini API - REQUIRED)
     */
    private function getAIResponse(string $prompt): string
    {
        try {
            $systemInstruction = 'Chuyên gia sức khỏe. Tạo kế hoạch cá nhân hóa. Mỗi ngày khác nhau. Trả JSON thuần, không markdown, không giải thích. TIẾNG VIỆT ngắn gọn.';
            
            $content = $this->geminiService->generateJsonContent(
                $prompt,
                $systemInstruction,
                [],
                [
                    'temperature' => 0.8,
                    'max_tokens' => 16384, // Max for gemini-2.5-flash
                    'timeout' => 180, // Tăng timeout lên 3 phút
                    'http_timeout' => 120, // Tăng HTTP timeout lên 2 phút
                    'model' => 'gemini-2.5-flash',
                    'retry' => 2 // Retry 2 lần nếu fail
                ]
            );
            
            Log::info('Gemini API successful response received');
            return $content;
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
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
            
            // Validate meal structure (support both old and new format)
            foreach ($dayPlan['meals'] as $meal) {
                // New format: meals have "options" array
                if (isset($meal['options'])) {
                    if (!is_array($meal['options']) || empty($meal['options'])) {
                        Log::error('Meal options invalid', ['meal' => $meal]);
                        throw new \RuntimeException('Lựa chọn bữa ăn không hợp lệ.');
                    }
                }
                // Old format: meals have "food" directly - this is still valid
            }
        }

        Log::info('AI plan parsed successfully', ['days_count' => count($decoded['daily_plans'])]);
        
        return $decoded;
    }
}

