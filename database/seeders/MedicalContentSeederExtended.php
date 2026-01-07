<?php

namespace Database\Seeders;

use App\Models\MedicalContent;
use Illuminate\Database\Seeder;

/**
 * Extended Seeder để tạo 200 bản ghi medical content
 * Chạy: php artisan db:seed --class=MedicalContentSeederExtended
 */
class MedicalContentSeederExtended extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ
        \DB::table('medical_content')->truncate();
        
        $this->command->info('Đã xóa dữ liệu cũ. Bắt đầu thêm 200 bản ghi mới...');

        // Tạo 120 Knowledge Base articles
        $this->createKnowledgeBaseArticles(120);
        
        // Tạo 60 FAQs
        $this->createFAQs(60);
        
        // Tạo 20 Templates
        $this->createTemplates(20);

        $this->command->info('Hoàn thành! Đã thêm 200 bản ghi medical content.');
    }

    private function createKnowledgeBaseArticles(int $count): void
    {
        $topics = $this->getKnowledgeBaseTopics();
        
        for ($i = 0; $i < $count; $i++) {
            $topic = $topics[$i % count($topics)];
            $variation = floor($i / count($topics));
            
            MedicalContent::create([
                'content_type' => 'knowledge_base',
                'title' => $variation > 0 ? $topic['title'] . ' - Phần ' . ($variation + 1) : $topic['title'],
                'content' => $topic['content'],
                'category' => $topic['category'],
                'tags' => $topic['tags'],
                'status' => 'published',
                'views_count' => rand(50, 1000),
                'helpful_count' => rand(5, 100),
            ]);
        }
        
        $this->command->info("Đã tạo {$count} bài viết Knowledge Base.");
    }

    private function createFAQs(int $count): void
    {
        $faqs = $this->getFAQTopics();
        
        for ($i = 0; $i < $count; $i++) {
            $faq = $faqs[$i % count($faqs)];
            $variation = floor($i / count($faqs));
            
            MedicalContent::create([
                'content_type' => 'faq',
                'title' => $variation > 0 ? $faq['title'] . ' (Câu hỏi ' . ($variation + 1) . ')' : $faq['title'],
                'content' => $faq['content'],
                'category' => $faq['category'],
                'status' => 'published',
                'views_count' => rand(30, 600),
                'helpful_count' => rand(3, 70),
            ]);
        }
        
        $this->command->info("Đã tạo {$count} FAQs.");
    }

    private function createTemplates(int $count): void
    {
        $templates = $this->getTemplateTopics();
        
        for ($i = 0; $i < $count; $i++) {
            $template = $templates[$i % count($templates)];
            $variation = floor($i / count($templates));
            
            MedicalContent::create([
                'content_type' => 'template',
                'title' => $variation > 0 ? $template['title'] . ' - Mẫu ' . ($variation + 1) : $template['title'],
                'content' => $template['content'],
                'category' => $template['category'],
                'specialty' => $template['specialty'],
                'status' => 'published',
            ]);
        }
        
        $this->command->info("Đã tạo {$count} Templates.");
    }

    private function getKnowledgeBaseTopics(): array
    {
        return [
            [
                'title' => 'Hiểu về bệnh tiểu đường và cách phòng ngừa',
                'content' => 'Bệnh tiểu đường (đái tháo đường) là một bệnh mãn tính ảnh hưởng đến cách cơ thể chuyển hóa glucose. Có hai loại chính: type 1 và type 2. Triệu chứng: khát nước nhiều, đi tiểu thường xuyên, mệt mỏi, giảm cân. Cách phòng ngừa: duy trì cân nặng hợp lý, tập thể dục 30 phút/ngày, ăn uống lành mạnh với nhiều rau xanh, hạn chế đường và tinh bột tinh chế, kiểm tra đường huyết định kỳ nếu có nguy cơ cao.',
                'category' => 'Bệnh mãn tính',
                'tags' => ['tiểu đường', 'đường huyết', 'sức khỏe', 'phòng ngừa'],
            ],
            [
                'title' => 'Cách duy trì huyết áp ổn định',
                'content' => 'Huyết áp cao có thể dẫn đến đột quỵ và bệnh tim. Cách duy trì: giảm muối dưới 5g/ngày, tập thể dục 30 phút/ngày, duy trì cân nặng hợp lý, hạn chế rượu bia, bỏ thuốc lá, quản lý căng thẳng, ngủ đủ 7-8 giờ/đêm, đo huyết áp thường xuyên. Nếu huyết áp > 140/90 mmHg, hãy tham khảo ý kiến bác sĩ.',
                'category' => 'Tim mạch',
                'tags' => ['huyết áp', 'tim mạch', 'sức khỏe tim'],
            ],
            // Thêm các topics khác...
        ];
    }

    private function getFAQTopics(): array
    {
        return [
            [
                'title' => 'Tôi nên khám sức khỏe định kỳ bao lâu một lần?',
                'content' => 'Người trưởng thành khỏe mạnh: 1 lần/năm. Người trung niên (40-65): 1-2 lần/năm. Người cao tuổi (trên 65): 2-4 lần/năm. Các xét nghiệm cần làm: xét nghiệm máu mỗi năm, đo huyết áp mỗi 6 tháng, khám mắt mỗi 2 năm, khám răng mỗi 6 tháng. Phụ nữ: khám phụ khoa mỗi năm, chụp nhũ ảnh từ 40 tuổi. Nam giới: khám tuyến tiền liệt từ 50 tuổi.',
                'category' => 'Khám sức khỏe',
            ],
            // Thêm các FAQs khác...
        ];
    }

    private function getTemplateTopics(): array
    {
        return [
            [
                'title' => 'Mẫu tư vấn bệnh tiểu đường',
                'content' => '1. Đánh giá: đo đường huyết, HbA1c, biến chứng. 2. Chế độ ăn: chia nhỏ bữa, hạn chế đường, tăng rau xanh. 3. Tập luyện: 150 phút/tuần. 4. Theo dõi: ghi nhật ký đường huyết. 5. Quản lý thuốc: uống đúng giờ, tái khám định kỳ.',
                'category' => 'Bệnh mãn tính',
                'specialty' => 'Nội tiết',
            ],
            // Thêm các templates khác...
        ];
    }
}

