<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingScenario;
use App\Models\User;

class TrainingScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy admin user đầu tiên hoặc user đầu tiên
        $adminUser = User::where('role', 'admin')->first() ?? User::first();

        $scenarios = [
            [
                'name' => 'Tình huống khẩn cấp',
                'description' => 'Huấn luyện AI để nhận diện và xử lý các tình huống y tế khẩn cấp như đau ngực, khó thở, mất ý thức.',
                'scenario_data' => [
                    'examples' => [
                        'Tôi đang đau ngực dữ dội và khó thở',
                        'Người nhà tôi bất tỉnh, không phản ứng',
                        'Tôi bị chảy máu nhiều không cầm được'
                    ],
                    'emergency_keywords' => ['đau ngực', 'khó thở', 'bất tỉnh', 'chảy máu', 'khẩn cấp'],
                    'response_template' => 'Đây là tình huống khẩn cấp. Vui lòng gọi cấp cứu ngay lập tức...'
                ],
                'status' => 'trained',
                'training_progress' => 100,
                'created_by' => $adminUser ? $adminUser->id : null,
            ],
            [
                'name' => 'Tư vấn tổng quát',
                'description' => 'Huấn luyện AI để tư vấn về các vấn đề sức khỏe thông thường như cảm cúm, đau đầu, mệt mỏi.',
                'scenario_data' => [
                    'examples' => [
                        'Tôi bị cảm cúm 3 ngày rồi, có cách nào giảm triệu chứng không?',
                        'Đau đầu thường xuyên, có phải do stress không?',
                        'Cảm thấy mệt mỏi suốt ngày, nguyên nhân là gì?'
                    ],
                    'topics' => ['cảm cúm', 'đau đầu', 'mệt mỏi', 'sức khỏe tổng quát'],
                ],
                'status' => 'trained',
                'training_progress' => 100,
                'created_by' => $adminUser ? $adminUser->id : null,
            ],
            [
                'name' => 'Tư vấn theo dõi',
                'description' => 'Huấn luyện AI để theo dõi tiến trình điều trị và đưa ra lời khuyên tiếp theo.',
                'scenario_data' => [
                    'examples' => [
                        'Tôi đã uống thuốc được 1 tuần, triệu chứng đã giảm',
                        'Sau khi điều trị, tôi cần làm gì tiếp theo?',
                        'Tình trạng của tôi có cải thiện không?'
                    ],
                ],
                'status' => 'trained',
                'training_progress' => 100,
                'created_by' => $adminUser ? $adminUser->id : null,
            ],
            [
                'name' => 'Phân tích triệu chứng',
                'description' => 'Huấn luyện AI để phân tích các triệu chứng và đưa ra các khả năng chẩn đoán.',
                'scenario_data' => [
                    'examples' => [
                        'Tôi có các triệu chứng: sốt, ho, đau họng',
                        'Phân tích triệu chứng của tôi: đau bụng, buồn nôn',
                        'Tôi có triệu chứng gì đó không rõ ràng'
                    ],
                ],
                'status' => 'trained',
                'training_progress' => 100,
                'created_by' => $adminUser ? $adminUser->id : null,
            ],
            [
                'name' => 'Tư vấn dinh dưỡng',
                'description' => 'Huấn luyện AI để tư vấn về chế độ dinh dưỡng và ăn uống lành mạnh.',
                'scenario_data' => [
                    'examples' => [
                        'Tôi muốn giảm cân, nên ăn gì?',
                        'Chế độ dinh dưỡng cho người tiểu đường',
                        'Thực phẩm nào tốt cho tim mạch?'
                    ],
                ],
                'status' => 'trained',
                'training_progress' => 95,
                'created_by' => $adminUser ? $adminUser->id : null,
            ],
            [
                'name' => 'Tư vấn sức khỏe tâm thần',
                'description' => 'Huấn luyện AI để hỗ trợ tư vấn về các vấn đề sức khỏe tâm thần.',
                'scenario_data' => [
                    'examples' => [
                        'Tôi cảm thấy lo âu và stress nhiều',
                        'Làm sao để cải thiện giấc ngủ?',
                        'Tôi có dấu hiệu trầm cảm không?'
                    ],
                ],
                'status' => 'training',
                'training_progress' => 60,
                'created_by' => $adminUser ? $adminUser->id : null,
            ],
        ];

        foreach ($scenarios as $scenario) {
            TrainingScenario::create($scenario);
        }
    }
}

