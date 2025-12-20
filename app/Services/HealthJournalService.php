<?php

namespace App\Services;

use App\Models\HealthJournal;
use App\Models\HealthProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HealthJournalService
{
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
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            // If no API key, return null and rely on rule-based analysis
            Log::info('Skipping AI analysis - no API key configured');
            return null;
        }

        try {
            set_time_limit(60);
            
            $prompt = $this->buildAnalysisPrompt($journal, $profile);
            
            $response = Http::timeout(40)
                ->withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional health analysis AI. Analyze health journal entries and provide helpful, actionable suggestions and warnings in ENGLISH. ALWAYS prioritize safety and recommend professional medical consultation when appropriate. Respond in JSON format only.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.6,
                    'max_tokens' => 800,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? null;
                if ($content) {
                    Log::info('AI Journal Analysis successful');
                    return $this->parseAIResponse($content);
                }
            } else {
                $error = $response->json()['error']['message'] ?? 'Unknown error';
                Log::error('OpenAI API Error in HealthJournal: ' . $error);
            }
        } catch (\Exception $e) {
            Log::error('Exception in HealthJournal AI analysis: ' . $e->getMessage());
            throw $e; // Re-throw to be caught by caller
        }

        return null;
    }

    /**
     * Build analysis prompt
     */
    private function buildAnalysisPrompt(HealthJournal $journal, ?HealthProfile $profile): string
    {
        $prompt = "Analyze this health journal entry from " . $journal->journal_date->format('Y-m-d') . " and provide comprehensive health insights:\n\n";
        
        $prompt .= "JOURNAL ENTRY:\n";
        if ($journal->symptoms) {
            $prompt .= "Symptoms: {$journal->symptoms}\n";
        }
        if ($journal->food_diary) {
            $prompt .= "Food Diary: {$journal->food_diary}\n";
        }
        if ($journal->exercise_log) {
            $prompt .= "Exercise Log: {$journal->exercise_log}\n";
        }
        if ($journal->mood) {
            $prompt .= "Mood: {$journal->mood}";
            if ($journal->mood_score) {
                $prompt .= " (Score: {$journal->mood_score}/10)";
            }
            $prompt .= "\n";
            if ($journal->mood_notes) {
                $prompt .= "Mood Notes: {$journal->mood_notes}\n";
            }
        }
        if ($journal->notes) {
            $prompt .= "Additional Notes: {$journal->notes}\n";
        }

        if ($profile) {
            $prompt .= "\nUSER PROFILE:\n";
            if ($profile->age) $prompt .= "- Age: {$profile->age}\n";
            if ($profile->gender) $prompt .= "- Gender: {$profile->gender}\n";
            if ($profile->bmi) $prompt .= "- BMI: {$profile->bmi}\n";
            if ($profile->medical_history) $prompt .= "- Medical History: {$profile->medical_history}\n";
            if ($profile->allergies) $prompt .= "- Allergies: {$profile->allergies}\n";
            if ($profile->health_goals) {
                $goals = is_array($profile->health_goals) ? implode(', ', $profile->health_goals) : $profile->health_goals;
                $prompt .= "- Health Goals: {$goals}\n";
            }
        }

        $prompt .= "\nANALYZE AND PROVIDE:\n";
        $prompt .= "1. Actionable health suggestions (3-5 items) based on the entry\n";
        $prompt .= "2. Warnings if any health risks detected\n";
        $prompt .= "3. Whether doctor consultation is recommended (true/false)\n";
        $prompt .= "4. Reason for doctor recommendation if applicable\n\n";
        
        $prompt .= "RETURN JSON FORMAT (REQUIRED):\n";
        $prompt .= '{"suggestions": ["suggestion 1", "suggestion 2", ...], "warnings": ["warning 1", ...], "doctor_recommended": true/false, "doctor_reason": "reason or null"}';
        
        $prompt .= "\n\nWrite in clear, concise ENGLISH. Prioritize user safety.";

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
                    return [
                        'type' => 'ai', 
                        'message' => is_string($s) ? '🤖 AI: ' . $s : $s
                    ];
                }, $decoded['suggestions']),
                'warnings' => isset($decoded['warnings']) && is_array($decoded['warnings']) ? array_map(function($w) {
                    return [
                        'level' => 'medium', 
                        'message' => is_string($w) ? '⚠️ AI: ' . $w : $w, 
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

