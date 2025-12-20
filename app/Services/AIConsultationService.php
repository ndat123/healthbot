<?php

namespace App\Services;

use App\Models\AIConsultation;
use App\Models\HealthProfile;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIConsultationService
{
    /**
     * Process user message and generate AI response
     */
    public function processMessage(
        User $user,
        string $message,
        ?string $sessionId = null,
        ?array $conversationHistory = []
    ): array {
        try {
            // Get or create session
            if (!$sessionId) {
                $sessionId = AIConsultation::generateSessionId();
            }

            // Get user's health profile for personalization
            $profile = null;
            try {
                $profile = HealthProfile::where('user_id', $user->id)->first();
            } catch (\Exception $e) {
                Log::warning('Could not fetch health profile: ' . $e->getMessage());
            }

            // Analyze message to determine consultation type
            $consultationType = $this->analyzeConsultationType($message);

            // Determine emergency level
            $emergencyLevel = $this->detectEmergencyLevel($message);

            // Build context from health profile
            $context = $this->buildContext($profile, $conversationHistory);

            // Build AI prompt
            $prompt = $this->buildPrompt($message, $context, $consultationType, $emergencyLevel);

            // Get AI response
            $aiResponse = $this->getAIResponse($prompt, $conversationHistory);

            // Extract suggested specialists if applicable
            $suggestedSpecialists = $this->extractSpecialistSuggestions($aiResponse, $consultationType);

            // Determine topic
            $topic = $this->extractTopic($message, $aiResponse);

            return [
                'session_id' => $sessionId,
                'topic' => $topic,
                'consultation_type' => $consultationType,
                'user_message' => $message,
                'ai_response' => $aiResponse,
                'emergency_level' => $emergencyLevel,
                'context_data' => $context,
                'suggested_specialists' => $suggestedSpecialists,
                'disclaimer_acknowledged' => false,
            ];
        } catch (\Exception $e) {
            Log::error('Error in processMessage: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return a safe fallback response
            return [
                'session_id' => $sessionId ?? AIConsultation::generateSessionId(),
                'topic' => 'Error',
                'consultation_type' => 'general',
                'user_message' => $message,
                'ai_response' => 'I apologize, but I encountered an error processing your message. Please try again or rephrase your question.',
                'emergency_level' => 'low',
                'context_data' => [],
                'suggested_specialists' => null,
                'disclaimer_acknowledged' => false,
            ];
        }
    }

    /**
     * Analyze message to determine consultation type
     */
    private function analyzeConsultationType(string $message): string
    {
        $message = strtolower($message);

        // Symptoms keywords
        $symptomKeywords = ['pain', 'ache', 'fever', 'cough', 'headache', 'nausea', 'dizziness', 'symptom', 'hurt', 'unwell'];
        if (Str::contains($message, $symptomKeywords)) {
            return 'symptoms';
        }

        // Nutrition keywords
        $nutritionKeywords = ['diet', 'food', 'nutrition', 'meal', 'eat', 'calorie', 'protein', 'vitamin', 'weight'];
        if (Str::contains($message, $nutritionKeywords)) {
            return 'nutrition';
        }

        // Lifestyle keywords
        $lifestyleKeywords = ['exercise', 'workout', 'sleep', 'stress', 'habit', 'routine', 'lifestyle'];
        if (Str::contains($message, $lifestyleKeywords)) {
            return 'lifestyle';
        }

        // Specialist referral keywords
        $specialistKeywords = ['doctor', 'specialist', 'refer', 'appointment', 'examination', 'checkup'];
        if (Str::contains($message, $specialistKeywords)) {
            return 'specialist_referral';
        }

        return 'general';
    }

    /**
     * Detect emergency level from message
     */
    private function detectEmergencyLevel(string $message): string
    {
        $message = strtolower($message);

        // Critical keywords
        $criticalKeywords = ['chest pain', 'heart attack', 'stroke', 'severe', 'unconscious', 'can\'t breathe', 'choking'];
        if (Str::contains($message, $criticalKeywords)) {
            return 'critical';
        }

        // High priority keywords
        $highKeywords = ['severe pain', 'high fever', 'difficulty breathing', 'bleeding', 'trauma', 'injury'];
        if (Str::contains($message, $highKeywords)) {
            return 'high';
        }

        // Medium priority keywords
        $mediumKeywords = ['persistent', 'worsening', 'chronic', 'concerned'];
        if (Str::contains($message, $mediumKeywords)) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Build context from health profile and conversation history
     */
    private function buildContext(?HealthProfile $profile, array $conversationHistory = []): array
    {
        $context = [
            'conversation_history' => $conversationHistory,
        ];

        if ($profile) {
            $context['user_profile'] = [
                'age' => $profile->age,
                'gender' => $profile->gender,
                'bmi' => $profile->bmi,
                'medical_history' => $profile->medical_history,
                'allergies' => $profile->allergies,
                'health_goals' => $profile->health_goals,
                'lifestyle_habits' => $profile->lifestyle_habits,
                'blood_pressure' => $profile->blood_pressure_systolic ? 
                    "{$profile->blood_pressure_systolic}/{$profile->blood_pressure_diastolic}" : null,
                'blood_sugar' => $profile->blood_sugar,
            ];
        }

        return $context;
    }

    /**
     * Build AI prompt
     */
    private function buildPrompt(
        string $message,
        array $context,
        string $consultationType,
        string $emergencyLevel
    ): string {
        $prompt = "You are AI HealthBot, a professional AI health consultant. Provide helpful, accurate, and personalized health advice in ENGLISH.\n\n";
        
        $prompt .= "CRITICAL: Always include medical disclaimers when discussing symptoms or medical conditions. For serious symptoms, immediately recommend seeing a doctor.\n\n";

        // Add user context if available
        if (isset($context['user_profile'])) {
            $profile = $context['user_profile'];
            $prompt .= "USER PROFILE:\n";
            if ($profile['age']) $prompt .= "- Age: {$profile['age']}\n";
            if ($profile['gender']) $prompt .= "- Gender: {$profile['gender']}\n";
            if ($profile['bmi']) $prompt .= "- BMI: {$profile['bmi']}\n";
            if ($profile['medical_history']) $prompt .= "- Medical History: {$profile['medical_history']}\n";
            if ($profile['allergies']) $prompt .= "- Allergies: {$profile['allergies']}\n";
            if ($profile['health_goals']) {
                $goals = is_array($profile['health_goals']) ? implode(', ', $profile['health_goals']) : $profile['health_goals'];
                $prompt .= "- Health Goals: {$goals}\n";
            }
            $prompt .= "\n";
        }

        // Add consultation type specific instructions
        switch ($consultationType) {
            case 'symptoms':
                $prompt .= "CONSULTATION TYPE: Symptoms inquiry. Provide initial guidance but EMPHASIZE the importance of professional medical evaluation. DO NOT diagnose.\n";
                break;
            case 'nutrition':
                $prompt .= "CONSULTATION TYPE: Nutrition advice. Provide personalized dietary recommendations based on their profile.\n";
                break;
            case 'lifestyle':
                $prompt .= "CONSULTATION TYPE: Lifestyle and habits. Provide actionable, personalized recommendations.\n";
                break;
            case 'specialist_referral':
                $prompt .= "CONSULTATION TYPE: Specialist referral. Suggest appropriate medical specialists based on their needs and symptoms.\n";
                break;
        }

        // Add emergency level warning
        if ($emergencyLevel === 'critical' || $emergencyLevel === 'high') {
            $prompt .= "\n⚠️ URGENT: The user's message suggests a SERIOUS health concern. BEGIN your response by STRONGLY recommending IMMEDIATE medical attention (call 911 or go to ER).\n";
        }

        $prompt .= "\nUser's message: {$message}\n\n";
        $prompt .= "REQUIREMENTS:\n";
        $prompt .= "1. Respond in clear, empathetic ENGLISH\n";
        $prompt .= "2. Include medical disclaimer if discussing symptoms/conditions\n";
        $prompt .= "3. Be concise but thorough (200-400 words)\n";
        $prompt .= "4. Use bullet points for clarity\n";
        $prompt .= "5. For emergencies: recommend immediate medical attention FIRST\n";

        return $prompt;
    }

    /**
     * Get AI response from OpenAI - REQUIRED
     */
    private function getAIResponse(string $prompt, array $conversationHistory = []): string
    {
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            throw new \RuntimeException('OPENAI_API_KEY is not configured in .env file. Please add your OpenAI API key.');
        }

        try {
            set_time_limit(60); // 1 minute for chat response
            
            // Build messages array for chat completion
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'You are AI HealthBot, a professional AI health consultant. ALWAYS include appropriate medical disclaimers when discussing medical conditions or symptoms. Be empathetic, clear, helpful, and respond in ENGLISH. For emergency symptoms (chest pain, stroke, severe bleeding), immediately recommend seeking emergency medical attention.'
                ]
            ];

            // Add conversation history (last 10 messages for context)
            foreach (array_slice($conversationHistory, -10) as $history) {
                $messages[] = ['role' => 'user', 'content' => $history['user_message']];
                $messages[] = ['role' => 'assistant', 'content' => $history['ai_response']];
            }

            // Add current message
            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = Http::timeout(45)
                ->withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 1500,
                ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? null;
                if (!$content) {
                    throw new \RuntimeException('OpenAI returned empty response');
                }
                Log::info('AI Consultation API successful');
                return $content;
            } else {
                $error = $response->json()['error']['message'] ?? 'Unknown API error';
                throw new \RuntimeException('OpenAI API error: ' . $error);
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Consultation API Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to get AI response: ' . $e->getMessage());
        }
    }

    /**
     * Mock AI response for development
     */
    private function getMockAIResponse(string $prompt): string
    {
        $message = strtolower($prompt);
        
        // Emergency response
        if (Str::contains($message, ['chest pain', 'heart', 'stroke', 'severe', 'critical'])) {
            return "⚠️ **URGENT MEDICAL ATTENTION REQUIRED**\n\n" .
                   "Based on your description, this may be a serious medical emergency. Please seek immediate medical attention:\n" .
                   "- Call emergency services (911) if symptoms are severe\n" .
                   "- Go to the nearest emergency room\n" .
                   "- Do not delay seeking professional medical help\n\n" .
                   "**Important Disclaimer**: This AI consultation does not replace professional medical evaluation. Always consult with qualified healthcare providers for medical concerns.";
        }

        // Symptoms response
        if (Str::contains($message, ['pain', 'ache', 'fever', 'symptom'])) {
            return "I understand you're experiencing some symptoms. Here's some initial guidance:\n\n" .
                   "**General Advice**:\n" .
                   "- Rest and stay hydrated\n" .
                   "- Monitor your symptoms closely\n" .
                   "- Keep track of when symptoms occur and their severity\n\n" .
                   "**When to See a Doctor**:\n" .
                   "- If symptoms persist for more than a few days\n" .
                   "- If symptoms worsen or become severe\n" .
                   "- If you experience any concerning changes\n\n" .
                   "**Important**: This is general advice only. For proper diagnosis and treatment, please consult with a healthcare professional.\n\n" .
                   "Would you like me to suggest which type of specialist might be appropriate for your symptoms?";
        }

        // Nutrition response
        if (Str::contains($message, ['diet', 'food', 'nutrition', 'meal', 'eat'])) {
            return "Great question about nutrition! Here's personalized dietary advice:\n\n" .
                   "**General Nutrition Guidelines**:\n" .
                   "- Eat a balanced diet with plenty of fruits and vegetables\n" .
                   "- Include lean proteins and whole grains\n" .
                   "- Stay hydrated with water throughout the day\n" .
                   "- Limit processed foods and added sugars\n\n" .
                   "**Personalized Recommendations**:\n" .
                   "Based on your health profile, I can provide more specific advice. Would you like me to create a personalized meal plan?\n\n" .
                   "**Note**: For specific dietary needs related to medical conditions, please consult with a registered dietitian or your healthcare provider.";
        }

        // Lifestyle response
        if (Str::contains($message, ['exercise', 'workout', 'sleep', 'stress', 'habit'])) {
            return "I'd be happy to help with lifestyle and healthy habits!\n\n" .
                   "**Healthy Lifestyle Tips**:\n" .
                   "- Aim for 7-9 hours of quality sleep per night\n" .
                   "- Engage in regular physical activity (at least 150 minutes per week)\n" .
                   "- Practice stress management techniques (meditation, deep breathing)\n" .
                   "- Maintain a regular routine\n\n" .
                   "**Personalized Suggestions**:\n" .
                   "Based on your profile, I can provide more specific lifestyle recommendations. What area would you like to focus on?\n\n" .
                   "Remember: Small, consistent changes lead to lasting improvements!";
        }

        // Default response
        return "Thank you for your question! I'm here to help with your health concerns.\n\n" .
               "I can assist you with:\n" .
               "- Understanding symptoms and when to seek medical care\n" .
               "- Nutrition and dietary advice\n" .
               "- Lifestyle and healthy habit recommendations\n" .
               "- Suggestions for appropriate medical specialists\n\n" .
               "**Important Medical Disclaimer**:\n" .
               "The information provided by AI HealthBot is for educational and informational purposes only. It is not intended to be a substitute for professional medical advice, diagnosis, or treatment. Always seek the advice of your physician or other qualified health provider with any questions you may have regarding a medical condition.\n\n" .
               "How can I help you today?";
    }

    /**
     * Extract specialist suggestions from AI response
     */
    private function extractSpecialistSuggestions(string $aiResponse, string $consultationType): ?array
    {
        if ($consultationType !== 'specialist_referral' && $consultationType !== 'symptoms') {
            return null;
        }

        $specialists = [];
        $response = strtolower($aiResponse);

        // Map keywords to specialists
        $specialistMap = [
            'cardiologist' => ['heart', 'cardiac', 'chest pain', 'blood pressure'],
            'dermatologist' => ['skin', 'rash', 'acne', 'dermatology'],
            'endocrinologist' => ['diabetes', 'thyroid', 'hormone', 'metabolism'],
            'gastroenterologist' => ['stomach', 'digestive', 'gut', 'abdomen'],
            'neurologist' => ['headache', 'brain', 'nerve', 'neurological'],
            'orthopedist' => ['bone', 'joint', 'muscle', 'orthopedic'],
            'pediatrician' => ['child', 'pediatric', 'infant'],
            'psychiatrist' => ['mental', 'depression', 'anxiety', 'psychiatric'],
        ];

        foreach ($specialistMap as $specialist => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($response, $keyword)) {
                    $specialists[] = $specialist;
                    break;
                }
            }
        }

        return !empty($specialists) ? array_unique($specialists) : null;
    }

    /**
     * Extract topic from message and response
     */
    private function extractTopic(string $message, string $response): string
    {
        $combined = strtolower($message . ' ' . $response);
        
        $topics = [
            'Headache' => ['headache', 'head pain', 'migraine'],
            'Fever' => ['fever', 'temperature', 'hot'],
            'Cough' => ['cough', 'coughing'],
            'Nutrition' => ['diet', 'food', 'nutrition', 'meal'],
            'Exercise' => ['exercise', 'workout', 'fitness'],
            'Sleep' => ['sleep', 'insomnia', 'rest'],
            'Stress' => ['stress', 'anxiety', 'worry'],
        ];

        foreach ($topics as $topic => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($combined, $keyword)) {
                    return $topic;
                }
            }
        }

        return 'General Health';
    }
}

