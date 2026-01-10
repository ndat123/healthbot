<?php

namespace App\Services;

use App\Models\AIConsultation;
use App\Models\AISession;
use Illuminate\Support\Facades\Log;

class AIMetricsService
{
    /**
     * Tự động tính toán và lưu accuracy_score cho một consultation
     * Dựa trên các yếu tố như emergency_level được xử lý đúng, chất lượng phản hồi, etc.
     */
    public function calculateAccuracyScore(AIConsultation $consultation): float
    {
        $baseScore = 85.0; // Điểm cơ bản
        
        // Điểm cộng/th trừ dựa trên emergency_level
        $emergencyBonus = 0.0;
        switch($consultation->emergency_level) {
            case 'low':
                $emergencyBonus = 5.0;
                break;
            case 'medium':
                $emergencyBonus = 0.0;
                break;
            case 'high':
                $emergencyBonus = -5.0;
                break;
            case 'critical':
                $emergencyBonus = -10.0;
                break;
            default:
                $emergencyBonus = 0.0;
        }
        
        // Điểm cộng nếu có suggested_specialists (AI đã phân tích tốt)
        $specialistBonus = $consultation->suggested_specialists ? 3.0 : 0.0;
        
        // Điểm cộng nếu có context_data (AI đã sử dụng thông tin người dùng)
        $contextBonus = !empty($consultation->context_data) ? 2.0 : 0.0;
        
        // Điểm trừ nếu response quá ngắn (có thể không đầy đủ)
        $responseLength = strlen($consultation->ai_response);
        $lengthPenalty = $responseLength < 100 ? -5.0 : 0.0;
        
        $finalScore = $baseScore + $emergencyBonus + $specialistBonus + $contextBonus + $lengthPenalty;
        
        // Giới hạn trong khoảng 70-100
        return max(70.0, min(100.0, $finalScore));
    }
    
    /**
     * Tự động tính toán user_satisfaction dựa trên các yếu tố
     */
    public function calculateUserSatisfaction(AIConsultation $consultation): float
    {
        $baseSatisfaction = 4.0; // Điểm cơ bản (trên thang 5)
        
        // Điểm cộng nếu response dài và chi tiết
        $responseLength = strlen($consultation->ai_response);
        $lengthBonus = min(0.5, $responseLength / 500); // Tối đa +0.5
        
        // Điểm cộng nếu có suggested_specialists
        $specialistBonus = $consultation->suggested_specialists ? 0.3 : 0.0;
        
        // Điểm trừ nếu emergency_level cao nhưng không được xử lý tốt
        $emergencyPenalty = 0.0;
        if (in_array($consultation->emergency_level, ['high', 'critical'])) {
            $emergencyPenalty = -0.2;
        }
        
        $finalSatisfaction = $baseSatisfaction + $lengthBonus + $specialistBonus + $emergencyPenalty;
        
        // Giới hạn trong khoảng 3.0-5.0
        return max(3.0, min(5.0, round($finalSatisfaction, 1)));
    }
    
    /**
     * Tạo AISession từ AIConsultation và tự động tính metrics
     */
    public function createSessionFromConsultation(AIConsultation $consultation): AISession
    {
        $accuracyScore = $this->calculateAccuracyScore($consultation);
        $userSatisfaction = $this->calculateUserSatisfaction($consultation);
        
        return AISession::create([
            'user_id' => $consultation->user_id,
            'session_token' => $consultation->session_id,
            'user_query' => $consultation->user_message,
            'ai_response' => $consultation->ai_response,
            'topic' => $consultation->topic,
            'emergency_level' => $consultation->emergency_level,
            'duration_seconds' => $consultation->duration_seconds,
            'accuracy_score' => $accuracyScore,
            'user_satisfaction' => $userSatisfaction,
            'metadata' => [
                'source' => 'auto_generated_from_consultation',
                'consultation_id' => $consultation->id,
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);
    }
    
    /**
     * Batch process: Tạo AISession cho tất cả consultations chưa có session
     */
    public function syncConsultationsToSessions(): int
    {
        $consultations = AIConsultation::all();
        
        $count = 0;
        foreach ($consultations as $consultation) {
            try {
                // Kiểm tra xem đã có session với session_token này chưa
                $existingSession = AISession::where('session_token', $consultation->session_id)->first();
                
                if (!$existingSession) {
                    $this->createSessionFromConsultation($consultation);
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error('Error creating AISession from consultation: ' . $e->getMessage(), [
                    'consultation_id' => $consultation->id,
                ]);
            }
        }
        
        return $count;
    }
}

