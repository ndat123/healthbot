<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AISession;
use App\Models\User;
use App\Models\AIConsultation;
use Carbon\Carbon;

class AISessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các user có consultations hoặc lấy user đầu tiên
        $users = User::limit(10)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('Không có user nào trong database. Vui lòng tạo user trước.');
            return;
        }

        $topics = [
            'Tư vấn sức khỏe tổng quát',
            'Phân tích triệu chứng',
            'Tư vấn dinh dưỡng',
            'Tư vấn về thuốc',
            'Tư vấn sức khỏe tâm thần',
        ];

        $emergencyLevels = ['low', 'medium', 'high', 'critical'];

        foreach ($users as $user) {
            // Tạo 2-5 sessions cho mỗi user
            $sessionCount = rand(2, 5);
            
            for ($i = 0; $i < $sessionCount; $i++) {
                $sessionToken = 'session_' . uniqid() . '_' . time() . '_' . $i;
                $topic = $topics[array_rand($topics)];
                $emergencyLevel = $emergencyLevels[array_rand($emergencyLevels)];
                
                // Tính accuracy_score dựa trên emergency_level và một số yếu tố khác
                // Giả sử accuracy cao hơn cho low/medium, thấp hơn cho high/critical
                $baseAccuracy = 85;
                switch($emergencyLevel) {
                    case 'low':
                        $baseAccuracy = rand(85, 95);
                        break;
                    case 'medium':
                        $baseAccuracy = rand(80, 90);
                        break;
                    case 'high':
                        $baseAccuracy = rand(75, 85);
                        break;
                    case 'critical':
                        $baseAccuracy = rand(70, 80);
                        break;
                    default:
                        $baseAccuracy = 85;
                }
                
                // Thêm một chút biến động
                $accuracyScore = $baseAccuracy + (rand(-5, 5) / 10);
                
                // User satisfaction: từ 3.5 đến 5.0
                $userSatisfaction = rand(35, 50) / 10;
                
                // Duration: từ 5 đến 30 giây
                $durationSeconds = rand(5, 30);
                
                AISession::create([
                    'user_id' => $user->id,
                    'session_token' => $sessionToken,
                    'user_query' => 'Câu hỏi mẫu về ' . strtolower($topic),
                    'ai_response' => 'Đây là phản hồi mẫu từ AI về ' . strtolower($topic) . '. AI đã phân tích và đưa ra lời khuyên phù hợp.',
                    'topic' => $topic,
                    'emergency_level' => $emergencyLevel,
                    'duration_seconds' => $durationSeconds,
                    'accuracy_score' => round($accuracyScore, 2),
                    'user_satisfaction' => $userSatisfaction,
                    'metadata' => [
                        'source' => 'seeder',
                        'created_at_seed' => Carbon::now()->toDateTimeString(),
                    ],
                    'created_at' => Carbon::now()->subDays(rand(0, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}

