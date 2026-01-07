<?php

namespace App\Services;

use App\Models\HealthJournal;
use App\Models\HealthProfile;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HealthJournalService
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Analyze journal entry and generate suggestions/warnings
     */
    public function analyzeJournalEntry(HealthJournal $journal, ?HealthProfile $profile = null): array
    {
        $analysis = [
            'suggestions' => [],
            'warnings' => [],
            'risk_level' => 'low',
            'doctor_recommended' => false,
            'doctor_recommendation_reason' => null,
        ];

        // Analyze symptoms
        if ($journal->symptoms) {
            $symptomAnalysis = $this->analyzeSymptoms($journal->symptoms);
            $analysis = array_merge_recursive($analysis, $symptomAnalysis);
        }

        // Analyze food diary
        if ($journal->food_diary) {
            $foodAnalysis = $this->analyzeFoodDiary($journal->food_diary, $profile);
            $analysis['suggestions'] = array_merge($analysis['suggestions'], $foodAnalysis['suggestions']);
        }

        // Analyze exercise log
        if ($journal->exercise_log) {
            $exerciseAnalysis = $this->analyzeExercise($journal->exercise_log, $profile);
            $analysis['suggestions'] = array_merge($analysis['suggestions'], $exerciseAnalysis['suggestions']);
        }

        // Analyze mood
        if ($journal->mood) {
            $moodAnalysis = $this->analyzeMood($journal->mood, $journal->mood_score, $journal->mood_notes);
            $analysis = array_merge_recursive($analysis, $moodAnalysis);
        }

        // Determine overall risk level
        $analysis['risk_level'] = $this->determineRiskLevel($analysis);

        // Generate AI suggestions (REQUIRED if API key is configured)
        try {
            $aiAnalysis = $this->getAIAnalysis($journal, $profile);
            if ($aiAnalysis) {
                $analysis = array_merge_recursive($analysis, $aiAnalysis);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to get AI analysis for health journal', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage(),
            ]);
            // Continue with rule-based analysis even if AI fails
        }

        // Normalize scalar fields that may have been turned into arrays by array_merge_recursive
        if (isset($analysis['doctor_recommended']) && is_array($analysis['doctor_recommended'])) {
            $last = end($analysis['doctor_recommended']);
            $analysis['doctor_recommended'] = (bool) $last;
        }

        if (isset($analysis['doctor_recommendation_reason']) && is_array($analysis['doctor_recommendation_reason'])) {
            $lastReason = end($analysis['doctor_recommendation_reason']);
            $analysis['doctor_recommendation_reason'] = $lastReason;
        }

        return $analysis;
    }

    /**
     * Analyze symptoms
     */
    private function analyzeSymptoms(string $symptoms): array
    {
        $symptomsLower = strtolower($symptoms);
        $analysis = [
            'warnings' => [],
            'suggestions' => [],
        ];

        // Critical symptoms
        $criticalKeywords = ['chest pain', 'difficulty breathing', 'severe pain', 'unconscious', 'stroke', 'heart attack'];
        foreach ($criticalKeywords as $keyword) {
            if (Str::contains($symptomsLower, $keyword)) {
                $analysis['warnings'][] = [
                    'level' => 'critical',
                    'message' => "⚠️ CRITICAL: You mentioned '{$keyword}'. Please seek immediate medical attention or call emergency services.",
                    'type' => 'emergency'
                ];
                $analysis['doctor_recommended'] = true;
                $analysis['doctor_recommendation_reason'] = "Critical symptoms detected: {$keyword}";
            }
        }

        // High priority symptoms
        $highKeywords = ['persistent fever', 'severe headache', 'worsening', 'chronic pain', 'bleeding'];
        foreach ($highKeywords as $keyword) {
            if (Str::contains($symptomsLower, $keyword)) {
                $analysis['warnings'][] = [
                    'level' => 'high',
                    'message' => "⚠️ HIGH PRIORITY: '{$keyword}' detected. Consider consulting a healthcare professional soon.",
                    'type' => 'symptom'
                ];
                if (!$analysis['doctor_recommended']) {
                    $analysis['doctor_recommended'] = true;
                    $analysis['doctor_recommendation_reason'] = "High priority symptoms: {$keyword}";
                }
            }
        }

        // Medium priority symptoms
        $mediumKeywords = ['mild pain', 'fatigue', 'dizziness', 'nausea'];
        foreach ($mediumKeywords as $keyword) {
            if (Str::contains($symptomsLower, $keyword)) {
                $analysis['warnings'][] = [
                    'level' => 'medium',
                    'message' => "ℹ️ Monitor: '{$keyword}' noted. Rest and observe. If symptoms persist, consider consulting a doctor.",
                    'type' => 'symptom'
                ];
            }
        }

        // General suggestions
        if (Str::contains($symptomsLower, ['cold', 'flu', 'cough'])) {
            $analysis['suggestions'][] = [
                'type' => 'lifestyle',
                'message' => '💧 Stay hydrated and get plenty of rest. Consider warm fluids and steam inhalation.',
            ];
        }

        return $analysis;
    }

    /**
     * Analyze food diary
     */
    private function analyzeFoodDiary(string $foodDiary, ?HealthProfile $profile): array
    {
        $foodLower = strtolower($foodDiary);
        $suggestions = [];

        // Check for unhealthy patterns
        $unhealthyKeywords = ['fast food', 'processed', 'sugar', 'soda', 'fried'];
        $unhealthyCount = 0;
        foreach ($unhealthyKeywords as $keyword) {
            if (Str::contains($foodLower, $keyword)) {
                $unhealthyCount++;
            }
        }

        if ($unhealthyCount > 2) {
            $suggestions[] = [
                'type' => 'nutrition',
                'message' => '🥗 Consider adding more whole foods, fruits, and vegetables to your diet. Limit processed foods.',
            ];
        }

        // Check for hydration
        if (!Str::contains($foodLower, ['water', 'fluid', 'hydration'])) {
            $suggestions[] = [
                'type' => 'hydration',
                'message' => '💧 Remember to drink plenty of water throughout the day (8-10 glasses recommended).',
            ];
        }

        // Check for protein
        if (!Str::contains($foodLower, ['protein', 'meat', 'fish', 'chicken', 'beans', 'tofu', 'eggs'])) {
            $suggestions[] = [
                'type' => 'nutrition',
                'message' => '🍗 Ensure adequate protein intake for muscle maintenance and overall health.',
            ];
        }

        // Profile-based suggestions
        if ($profile) {
            if ($profile->health_goals) {
                $goals = is_array($profile->health_goals) ? $profile->health_goals : [$profile->health_goals];
                if (in_array('weight_loss', $goals)) {
                    $suggestions[] = [
                        'type' => 'goal',
                        'message' => '🎯 For weight loss: Focus on portion control and include more fiber-rich foods.',
                    ];
                }
            }

            if ($profile->allergies) {
                $suggestions[] = [
                    'type' => 'safety',
                    'message' => '⚠️ Remember to avoid foods you are allergic to.',
                ];
            }
        }

        return ['suggestions' => $suggestions];
    }

    /**
     * Analyze exercise log
     */
    private function analyzeExercise(string $exerciseLog, ?HealthProfile $profile): array
    {
        $exerciseLower = strtolower($exerciseLog);
        $suggestions = [];

        // Check for activity
        if (empty(trim($exerciseLog)) || Str::contains($exerciseLower, ['none', 'rest', 'no exercise'])) {
            $suggestions[] = [
                'type' => 'activity',
                'message' => '🏃 Aim for at least 30 minutes of moderate activity daily. Even a short walk counts!',
            ];
        }

        // Check for variety
        $activityTypes = ['walk', 'run', 'gym', 'yoga', 'swim', 'bike', 'dance'];
        $foundTypes = 0;
        foreach ($activityTypes as $type) {
            if (Str::contains($exerciseLower, $type)) {
                $foundTypes++;
            }
        }

        if ($foundTypes === 0 && !empty(trim($exerciseLog))) {
            $suggestions[] = [
                'type' => 'variety',
                'message' => '💪 Great job exercising! Consider adding variety to your routine for better overall fitness.',
            ];
        }

        // Profile-based suggestions
        if ($profile && $profile->lifestyle_habits) {
            $habits = is_array($profile->lifestyle_habits) ? $profile->lifestyle_habits : [];
            $exerciseFreq = $habits['exercise_frequency'] ?? null;
            
            if ($exerciseFreq === 'none' || $exerciseFreq === '1-2') {
                $suggestions[] = [
                    'type' => 'consistency',
                    'message' => '📅 Try to establish a regular exercise routine. Consistency is key to long-term health benefits.',
                ];
            }
        }

        return ['suggestions' => $suggestions];
    }

    /**
     * Analyze mood
     */
    private function analyzeMood(?string $mood, ?int $moodScore, ?string $moodNotes): array
    {
        $analysis = [
            'warnings' => [],
            'suggestions' => [],
        ];

        // Low mood analysis
        if (in_array($mood, ['poor', 'very_poor']) || ($moodScore && $moodScore <= 3)) {
            $analysis['warnings'][] = [
                'level' => 'medium',
                'message' => '💙 Your mood seems low today. Consider talking to someone you trust or a mental health professional.',
                'type' => 'mental_health'
            ];

            $analysis['suggestions'][] = [
                'type' => 'mental_health',
                'message' => '🧘 Practice self-care: deep breathing, meditation, or activities you enjoy can help improve mood.',
            ];

            if ($mood === 'very_poor' || ($moodScore && $moodScore <= 2)) {
                $analysis['doctor_recommended'] = true;
                $analysis['doctor_recommendation_reason'] = 'Persistent low mood - consider mental health consultation';
            }
        }

        // Check mood notes for concerning keywords
        if ($moodNotes) {
            $notesLower = strtolower($moodNotes);
            $concerningKeywords = ['depressed', 'anxious', 'overwhelmed', 'hopeless', 'suicidal'];
            foreach ($concerningKeywords as $keyword) {
                if (Str::contains($notesLower, $keyword)) {
                    $analysis['warnings'][] = [
                        'level' => 'high',
                        'message' => "⚠️ Your mood notes mention '{$keyword}'. Please consider speaking with a mental health professional.",
                        'type' => 'mental_health'
                    ];
                    $analysis['doctor_recommended'] = true;
                    $analysis['doctor_recommendation_reason'] = "Mental health concerns detected in mood notes";
                    break;
                }
            }
        }

        // Positive reinforcement
        if (in_array($mood, ['excellent', 'good']) || ($moodScore && $moodScore >= 7)) {
            $analysis['suggestions'][] = [
                'type' => 'positive',
                'message' => '✨ Great to see you\'re feeling good! Keep up the positive habits.',
            ];
        }

        return $analysis;
    }

    /**
     * Determine overall risk level
     */
    private function determineRiskLevel(array $analysis): string
    {
        $warnings = $analysis['warnings'] ?? [];
        
        foreach ($warnings as $warning) {
            if ($warning['level'] === 'critical') {
                return 'critical';
            }
            if ($warning['level'] === 'high') {
                return 'high';
            }
        }

        if (count($warnings) > 0) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get AI analysis - REQUIRED for comprehensive journal analysis
     */
    private function getAIAnalysis(HealthJournal $journal, ?HealthProfile $profile): ?array
    {
        try {
            $prompt = $this->buildAnalysisPrompt($journal, $profile);
            
            $systemInstruction = 'Bạn là một AI phân tích sức khỏe chuyên nghiệp. QUAN TRỌNG: TẤT CẢ phản hồi PHẢI bằng TIẾNG VIỆT. KHÔNG được sử dụng tiếng Anh. Phân tích các mục nhật ký sức khỏe và cung cấp các gợi ý và cảnh báo hữu ích, có thể thực hiện được bằng TIẾNG VIỆT. LUÔN ưu tiên an toàn và khuyên nên tư vấn y tế chuyên nghiệp khi phù hợp. Chỉ phản hồi ở định dạng JSON. Tất cả nội dung trong suggestions, warnings, doctor_reason PHẢI là tiếng Việt.';
            
            $content = $this->geminiService->generateJsonContent(
                $prompt,
                $systemInstruction,
                [],
                [
                    'temperature' => 0.6,
                    'max_tokens' => 800,
                    'timeout' => 60,
                    'http_timeout' => 40,
                    'model' => 'gemini-2.5-flash'
                ]
            );
            
            if ($content) {
                Log::info('AI Journal Analysis successful');
                return $this->parseAIResponse($content);
            }
        } catch (\Exception $e) {
            Log::error('Exception in HealthJournal AI analysis: ' . $e->getMessage());
            // Return null to fall back to rule-based analysis
            return null;
        }

        return null;
    }

    /**
     * Build analysis prompt
     */
    private function buildAnalysisPrompt(HealthJournal $journal, ?HealthProfile $profile): string
    {
        $prompt = "Phân tích mục nhật ký sức khỏe này từ ngày " . $journal->journal_date->format('d/m/Y') . " và cung cấp những hiểu biết toàn diện về sức khỏe:\n\n";
        
        $prompt .= "MỤC NHẬT KÝ:\n";
        if ($journal->symptoms) {
            $prompt .= "Triệu chứng: {$journal->symptoms}\n";
        }
        if ($journal->food_diary) {
            $prompt .= "Nhật ký ăn uống: {$journal->food_diary}\n";
        }
        if ($journal->exercise_log) {
            $prompt .= "Nhật ký tập luyện: {$journal->exercise_log}\n";
        }
        if ($journal->mood) {
            $prompt .= "Tâm trạng: {$journal->mood}";
            if ($journal->mood_score) {
                $prompt .= " (Điểm: {$journal->mood_score}/10)";
            }
            $prompt .= "\n";
            if ($journal->mood_notes) {
                $prompt .= "Ghi chú tâm trạng: {$journal->mood_notes}\n";
            }
        }
        if ($journal->notes) {
            $prompt .= "Ghi chú bổ sung: {$journal->notes}\n";
        }

        if ($profile) {
            $prompt .= "\nHỒ SƠ NGƯỜI DÙNG:\n";
            if ($profile->age) $prompt .= "- Tuổi: {$profile->age}\n";
            if ($profile->gender) $prompt .= "- Giới tính: {$profile->gender}\n";
            if ($profile->bmi) $prompt .= "- BMI: {$profile->bmi}\n";
            if ($profile->medical_history) $prompt .= "- Tiền sử bệnh: {$profile->medical_history}\n";
            if ($profile->allergies) $prompt .= "- Dị ứng: {$profile->allergies}\n";
            if ($profile->health_goals) {
                $goals = is_array($profile->health_goals) ? implode(', ', $profile->health_goals) : $profile->health_goals;
                $prompt .= "- Mục tiêu sức khỏe: {$goals}\n";
            }
        }

        $prompt .= "\nPHÂN TÍCH VÀ CUNG CẤP (TẤT CẢ PHẢI BẰNG TIẾNG VIỆT):\n";
        $prompt .= "1. Các gợi ý sức khỏe có thể thực hiện (3-5 mục) dựa trên mục nhật ký - VIẾT BẰNG TIẾNG VIỆT\n";
        $prompt .= "2. Cảnh báo nếu phát hiện bất kỳ rủi ro sức khỏe nào - VIẾT BẰNG TIẾNG VIỆT\n";
        $prompt .= "3. Có nên tư vấn bác sĩ hay không (true/false)\n";
        $prompt .= "4. Lý do khuyên tư vấn bác sĩ nếu có - VIẾT BẰNG TIẾNG VIỆT\n\n";
        
        $prompt .= "TRẢ VỀ ĐỊNH DẠNG JSON (BẮT BUỘC):\n";
        $prompt .= '{"suggestions": ["gợi ý 1 bằng tiếng Việt", "gợi ý 2 bằng tiếng Việt", ...], "warnings": ["cảnh báo 1 bằng tiếng Việt", ...], "doctor_recommended": true/false, "doctor_reason": "lý do bằng tiếng Việt hoặc null"}';
        
        $prompt .= "\n\nQUAN TRỌNG: TẤT CẢ suggestions, warnings, doctor_reason PHẢI viết bằng TIẾNG VIỆT. KHÔNG được sử dụng tiếng Anh. Viết rõ ràng, ngắn gọn. Ưu tiên an toàn người dùng.";

        return $prompt;
    }

    /**
     * Parse AI response
     */
    private function parseAIResponse(string $content): array
    {
        // Try to parse JSON
        $decoded = json_decode($content, true);
        
        // If not valid JSON, try to extract JSON from text
        if (!$decoded) {
            preg_match('/\{.*\}/s', $content, $matches);
            if (!empty($matches[0])) {
                $decoded = json_decode($matches[0], true);
            }
        }
        
        if ($decoded && isset($decoded['suggestions']) && is_array($decoded['suggestions'])) {
            return [
                'suggestions' => array_map(function($s) {
                    $message = is_string($s) ? $s : (is_array($s) ? ($s['message'] ?? '') : '');
                    // Remove any existing AI prefix
                    $message = preg_replace('/^🤖\s*AI:\s*/i', '', $message);
                    return [
                        'type' => 'ai', 
                        'message' => $message
                    ];
                }, $decoded['suggestions']),
                'warnings' => isset($decoded['warnings']) && is_array($decoded['warnings']) ? array_map(function($w) {
                    $message = is_string($w) ? $w : (is_array($w) ? ($w['message'] ?? '') : '');
                    // Remove any existing AI prefix
                    $message = preg_replace('/^⚠️\s*AI:\s*/i', '', $message);
                    return [
                        'level' => 'medium', 
                        'message' => $message, 
                        'type' => 'ai'
                    ];
                }, $decoded['warnings']) : [],
                'doctor_recommended' => $decoded['doctor_recommended'] ?? false,
                'doctor_recommendation_reason' => $decoded['doctor_reason'] ?? null,
            ];
        }

        Log::warning('Failed to parse AI response for health journal', ['content' => $content]);
        return [];
    }
}

