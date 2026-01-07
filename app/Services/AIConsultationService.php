<?php

namespace App\Services;

use App\Models\AIConsultation;
use App\Models\HealthProfile;
use App\Models\User;
use App\Helpers\SettingsHelper;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIConsultationService
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
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

            // Get AI response (sử dụng language từ settings)
            $language = SettingsHelper::getUserLanguage($user);
            $aiResponse = $this->getAIResponse($prompt, $conversationHistory, $language);

            // Extract suggested specialists if applicable
            $suggestedSpecialists = $this->extractSpecialistSuggestions($aiResponse, $consultationType);

            // Determine topic
            $topic = $this->extractTopic($message, $aiResponse);
            
            // Lưu interaction cho AI learning nếu user cho phép
            if (SettingsHelper::allowAILearning($user)) {
                // Log interaction để cải thiện AI (có thể lưu vào database sau)
                Log::info('AI learning interaction saved', [
                    'user_id' => $user->id,
                    'consultation_type' => $consultationType,
                    'topic' => $topic,
                ]);
            }

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
                'ai_response' => 'Xin lỗi, tôi đã gặp lỗi khi xử lý tin nhắn của bạn. Vui lòng thử lại hoặc diễn đạt lại câu hỏi của bạn.',
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
        $prompt = "Bạn là AI HealthBot, một chuyên gia tư vấn sức khỏe AI chuyên nghiệp. Cung cấp lời khuyên sức khỏe hữu ích, chính xác và cá nhân hóa bằng TIẾNG VIỆT.\n\n";
        
        $prompt .= "QUAN TRỌNG: Luôn bao gồm tuyên bố từ chối trách nhiệm y tế khi thảo luận về triệu chứng hoặc tình trạng y tế. Đối với các triệu chứng nghiêm trọng, ngay lập tức khuyên nên gặp bác sĩ.\n\n";

        // Add user context if available
        if (isset($context['user_profile'])) {
            $profile = $context['user_profile'];
            $prompt .= "HỒ SƠ NGƯỜI DÙNG:\n";
            if ($profile['age']) $prompt .= "- Tuổi: {$profile['age']}\n";
            if ($profile['gender']) $prompt .= "- Giới tính: {$profile['gender']}\n";
            if ($profile['bmi']) $prompt .= "- BMI: {$profile['bmi']}\n";
            if ($profile['medical_history']) $prompt .= "- Tiền sử bệnh: {$profile['medical_history']}\n";
            if ($profile['allergies']) $prompt .= "- Dị ứng: {$profile['allergies']}\n";
            if ($profile['health_goals']) {
                $goals = is_array($profile['health_goals']) ? implode(', ', $profile['health_goals']) : $profile['health_goals'];
                $prompt .= "- Mục tiêu sức khỏe: {$goals}\n";
            }
            $prompt .= "\n";
        }

        // Add consultation type specific instructions
        switch ($consultationType) {
            case 'symptoms':
                $prompt .= "LOẠI TƯ VẤN: Hỏi về triệu chứng. Cung cấp hướng dẫn ban đầu nhưng NHẤN MẠNH tầm quan trọng của đánh giá y tế chuyên nghiệp. KHÔNG được chẩn đoán.\n";
                break;
            case 'nutrition':
                $prompt .= "LOẠI TƯ VẤN: Tư vấn dinh dưỡng. Cung cấp khuyến nghị chế độ ăn uống cá nhân hóa dựa trên hồ sơ của họ.\n";
                break;
            case 'lifestyle':
                $prompt .= "LOẠI TƯ VẤN: Lối sống và thói quen. Cung cấp các khuyến nghị có thể thực hiện được, cá nhân hóa.\n";
                break;
            case 'specialist_referral':
                $prompt .= "LOẠI TƯ VẤN: Giới thiệu chuyên khoa. Đề xuất các chuyên gia y tế phù hợp dựa trên nhu cầu và triệu chứng của họ.\n";
                break;
        }

        // Add emergency level warning
        if ($emergencyLevel === 'critical' || $emergencyLevel === 'high') {
            $prompt .= "\n⚠️ KHẨN CẤP: Tin nhắn của người dùng cho thấy một vấn đề sức khỏe NGHIÊM TRỌNG. BẮT ĐẦU phản hồi của bạn bằng cách MẠNH MẼ khuyên nên tìm kiếm sự chăm sóc y tế NGAY LẬP TỨC (gọi 115 hoặc đến phòng cấp cứu).\n";
        }

        $prompt .= "\nTin nhắn của người dùng: {$message}\n\n";
        $prompt .= "YÊU CẦU:\n";
        $prompt .= "1. Phản hồi bằng TIẾNG VIỆT rõ ràng, đồng cảm\n";
        $prompt .= "2. Bao gồm tuyên bố từ chối trách nhiệm y tế nếu thảo luận về triệu chứng/tình trạng\n";
        $prompt .= "3. Ngắn gọn nhưng đầy đủ (200-400 từ)\n";
        $prompt .= "4. Sử dụng dấu đầu dòng để rõ ràng\n";
        $prompt .= "5. Đối với trường hợp khẩn cấp: khuyên tìm kiếm sự chăm sóc y tế ngay lập tức TRƯỚC TIÊN\n";

        return $prompt;
    }

    /**
     * Get AI response from Gemini API
     */
    private function getAIResponse(string $prompt, array $conversationHistory = [], string $language = 'vi'): string
    {
        try {
            // Build system instruction với language setting
            $languageMap = [
                'vi' => 'TIẾNG VIỆT',
                'en' => 'ENGLISH',
                'es' => 'ESPAÑOL',
                'fr' => 'FRANÇAIS',
            ];
            $responseLanguage = $languageMap[$language] ?? 'TIẾNG VIỆT';
            
            $systemInstruction = "Bạn là AI HealthBot, một chuyên gia tư vấn sức khỏe AI chuyên nghiệp. LUÔN bao gồm các tuyên bố từ chối trách nhiệm y tế phù hợp khi thảo luận về tình trạng y tế hoặc triệu chứng. Hãy đồng cảm, rõ ràng, hữu ích và phản hồi bằng {$responseLanguage}. Đối với các triệu chứng khẩn cấp (đau ngực, đột quỵ, chảy máu nghiêm trọng), ngay lập tức khuyên nên tìm kiếm sự chăm sóc y tế khẩn cấp.";

            // Convert conversation history to format expected by GeminiService
            $formattedHistory = [];
            foreach (array_slice($conversationHistory, -10) as $history) {
                if (isset($history['user_message'])) {
                    $formattedHistory[] = [
                        'user_message' => $history['user_message'],
                        'ai_response' => $history['ai_response'] ?? ''
                    ];
                }
            }

            // Use GeminiService to generate response
            $content = $this->geminiService->generateContent(
                $prompt,
                $systemInstruction,
                $formattedHistory,
                [
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                    'timeout' => 120,
                    'http_timeout' => 90,
                    'model' => 'gemini-2.5-flash',
                    'retry' => 2
                ]
            );

            Log::info('AI Consultation API successful');
            return $content;
        } catch (\Exception $e) {
            Log::error('Gemini Consultation API Error: ' . $e->getMessage());
            throw new \RuntimeException('Không thể lấy phản hồi từ AI: ' . $e->getMessage());
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

