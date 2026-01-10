<?php

namespace App\Http\Controllers;

use App\Models\AIConsultation;
use App\Models\HealthProfile;
use App\Services\AIConsultationService;
use App\Services\AIMetricsService;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AIConsultationController extends Controller
{
    protected $aiService;
    protected $metricsService;

    public function __construct(AIConsultationService $aiService, AIMetricsService $metricsService)
    {
        $this->aiService = $aiService;
        $this->metricsService = $metricsService;
    }

    /**
     * Show AI consultation chat interface
     */
    public function index()
    {
        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();
        
        // Get recent consultations
        $recentConsultations = AIConsultation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('ai-consultation.index', compact('profile', 'recentConsultations'));
    }

    /**
     * Start a new consultation session
     */
    public function startSession(Request $request)
    {
        $user = Auth::user();
        $sessionId = AIConsultation::generateSessionId();
        
        // Lấy ngôn ngữ từ settings
        $language = SettingsHelper::getUserLanguage($user);
        
        // Welcome message theo ngôn ngữ
        $welcomeMessages = [
            'vi' => "Xin chào! Tôi là AI HealthBot, chuyên gia tư vấn sức khỏe AI của bạn. Tôi có thể giúp bạn:\n\n" .
                   "• Hiểu về các triệu chứng và vấn đề sức khỏe\n" .
                   "• Tư vấn dinh dưỡng cá nhân hóa\n" .
                   "• Khuyến nghị về lối sống và thói quen lành mạnh\n" .
                   "• Đề xuất các chuyên khoa y tế phù hợp\n\n" .
                   "**Quan trọng**: Lời khuyên của tôi chỉ mang tính chất thông tin và không thay thế cho tư vấn y tế chuyên nghiệp.\n\n" .
                   "Tôi có thể giúp gì cho bạn hôm nay?",
            // 'en' => "Hello! I'm AI HealthBot, your AI health consultant. I'm here to help you with:\n\n" .
            //        "• Understanding symptoms and health concerns\n" .
            //        "• Personalized nutrition advice\n" .
            //        "• Lifestyle and healthy habit recommendations\n" .
            //        "• Suggestions for appropriate medical specialists\n\n" .
            //        "**Important**: My advice is for informational purposes only and does not replace professional medical consultation.\n\n" .
            //        "How can I help you today?",
        ];
        
        $welcomeMessage = $welcomeMessages[$language] ?? $welcomeMessages['vi'];

        return response()->json([
            'session_id' => $sessionId,
            'message' => $welcomeMessage,
            'disclaimer_required' => true,
        ]);
    }

    /**
     * Send message to AI and get response
     */
    public function sendMessage(Request $request)
    {
        try {
            // Handle boolean conversion for disclaimer_acknowledged
            $disclaimerAcknowledged = $request->input('disclaimer_acknowledged');
            if (is_string($disclaimerAcknowledged)) {
                $disclaimerAcknowledged = filter_var($disclaimerAcknowledged, FILTER_VALIDATE_BOOLEAN);
            } else {
                $disclaimerAcknowledged = (bool) $disclaimerAcknowledged;
            }

            $validated = $request->validate([
                'message' => 'required|string|max:2000',
                'session_id' => 'nullable|string',
            ]);

            if (!$disclaimerAcknowledged) {
                return response()->json([
                    'error' => 'Vui lòng xác nhận tuyên bố từ chối trách nhiệm y tế trước khi tiếp tục.',
                ], 400);
            }

            $user = Auth::user();
            $sessionId = $validated['session_id'] ?? AIConsultation::generateSessionId();

            // Get conversation history for this session
            $conversationHistory = [];
            try {
                $conversationHistory = AIConsultation::where('session_id', $sessionId)
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function ($consultation) {
                        return [
                            'user_message' => $consultation->user_message,
                            'ai_response' => $consultation->ai_response,
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Error fetching conversation history: ' . $e->getMessage());
            }

            // Process message with AI
            $startTime = microtime(true);
            try {
                $result = $this->aiService->processMessage(
                    $user,
                    $validated['message'],
                    $sessionId,
                    $conversationHistory
                );
            } catch (\Throwable $e) {
                Log::error('Failed to process AI consultation', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                
                // Kiểm tra nếu là lỗi overload, trả về message thân thiện
                $errorMessage = $e->getMessage();
                if (strpos($errorMessage, 'overloaded') !== false || strpos($errorMessage, '503') !== false) {
                    return response()->json([
                        'response' => 'Xin lỗi, hệ thống AI hiện đang quá tải. Vui lòng thử lại sau vài giây.\n\n' .
                                     'Trong khi chờ đợi, bạn có thể:\n' .
                                     '• Xem lại các câu hỏi thường gặp\n' .
                                     '• Tạo kế hoạch sức khỏe cá nhân hóa\n' .
                                     '• Tham khảo kiến thức y tế trong phần Medical Content',
                        'session_id' => $sessionId,
                        'emergency_level' => 'low',
                        'suggested_specialists' => null,
                    ], 200); // Trả về 200 với fallback message
                }
                
                return response()->json([
                    'error' => 'Không thể nhận phản hồi từ AI. Vui lòng kiểm tra API key hoặc thử lại sau.',
                    'details' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
            $duration = round(microtime(true) - $startTime);

            // Save consultation
            try {
                $consultation = AIConsultation::create([
                    'user_id' => $user->id,
                    'health_profile_id' => ($profile = HealthProfile::where('user_id', $user->id)->first()) ? $profile->id : null,
                    'session_id' => $sessionId,
                    'topic' => $result['topic'] ?? 'General',
                    'consultation_type' => $result['consultation_type'] ?? 'general',
                    'user_message' => $result['user_message'],
                    'ai_response' => $result['ai_response'],
                    'emergency_level' => $result['emergency_level'] ?? 'low',
                    'context_data' => $result['context_data'] ?? [],
                    'suggested_specialists' => $result['suggested_specialists'] ?? null,
                    'disclaimer_acknowledged' => $disclaimerAcknowledged,
                    'message_count' => count($conversationHistory) + 1,
                    'duration_seconds' => $duration,
                ]);
                
                // Tự động tạo AISession với accuracy_score và user_satisfaction
                try {
                    $this->metricsService->createSessionFromConsultation($consultation);
                } catch (\Exception $e) {
                    Log::warning('Error creating AISession from consultation: ' . $e->getMessage());
                    // Continue even if session creation fails
                }
            } catch (\Exception $e) {
                Log::error('Error saving consultation: ' . $e->getMessage());
                // Continue even if save fails
            }

            return response()->json([
                'session_id' => $sessionId,
                'response' => $result['ai_response'],
                'consultation_type' => $result['consultation_type'] ?? 'general',
                'emergency_level' => $result['emergency_level'] ?? 'low',
                'suggested_specialists' => $result['suggested_specialists'] ?? null,
                'topic' => $result['topic'] ?? 'General',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Lỗi xác thực: ' . implode(', ', $e->errors()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('AI Consultation Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Đã xảy ra lỗi khi xử lý yêu cầu của bạn. Vui lòng thử lại.',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get conversation history for a session
     */
    public function getHistory($sessionId)
    {
        $user = Auth::user();
        
        $consultations = AIConsultation::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'conversation' => $consultations->map(function ($consultation) {
                return [
                    'id' => $consultation->id,
                    'user_message' => $consultation->user_message,
                    'ai_response' => $consultation->ai_response,
                    'consultation_type' => $consultation->consultation_type,
                    'emergency_level' => $consultation->emergency_level,
                    'suggested_specialists' => $consultation->suggested_specialists,
                    'created_at' => $consultation->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Get consultation statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_consultations' => AIConsultation::where('user_id', $user->id)->count(),
            'total_sessions' => AIConsultation::where('user_id', $user->id)
                ->distinct('session_id')
                ->count('session_id'),
            'by_type' => AIConsultation::where('user_id', $user->id)
                ->selectRaw('consultation_type, COUNT(*) as count')
                ->groupBy('consultation_type')
                ->pluck('count', 'consultation_type')
                ->toArray(),
        ];

        return response()->json($stats);
    }

    /**
     * Delete consultation session
     */
    public function destroy($sessionId)
    {
        $user = Auth::user();
        
        // Delete all consultations in this session
        $deleted = AIConsultation::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->delete();

        if ($deleted > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa consultation session thành công.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy consultation session để xóa.',
        ], 404);
    }
}

