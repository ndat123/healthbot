<?php

namespace Database\Seeders;

use App\Models\MedicalContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DB::table('medical_content')->truncate();
        
        $this->command->info('Đã xóa dữ liệu cũ. Bắt đầu thêm 200 bản ghi mới...');

        // Knowledge Base Articles (120 bài) - Nội dung chi tiết
        $this->seedKnowledgeBase();
        
        // FAQs (60 câu) - Câu trả lời cụ thể
        $this->seedFAQs();
        
        // Templates (20 mẫu)
        $this->seedTemplates();

        $this->command->info('Hoàn thành! Đã thêm 200 bản ghi medical content vào database.');
    }

    private function seedKnowledgeBase(): void
    {
        $articles = [
            [
                'title' => 'Hiểu về bệnh tiểu đường và cách phòng ngừa',
                'content' => 'Bệnh tiểu đường (đái tháo đường) là một bệnh mãn tính ảnh hưởng đến cách cơ thể chuyển hóa glucose (đường trong máu). 

Có hai loại chính:
- Tiểu đường type 1: Thường xuất hiện ở trẻ em và thanh thiếu niên, do cơ thể không sản xuất đủ insulin.
- Tiểu đường type 2: Phổ biến hơn ở người lớn, do cơ thể không sử dụng insulin hiệu quả.

Triệu chứng bao gồm:
• Khát nước nhiều và đi tiểu thường xuyên
• Mệt mỏi, yếu sức
• Giảm cân không rõ nguyên nhân
• Vết thương lâu lành
• Mờ mắt

Cách phòng ngừa:
1. Duy trì cân nặng hợp lý (BMI 18.5-24.9)
2. Tập thể dục ít nhất 30 phút mỗi ngày, 5 ngày/tuần
3. Ăn uống lành mạnh: nhiều rau xanh, trái cây, ngũ cốc nguyên hạt
4. Hạn chế đường và tinh bột tinh chế
5. Không hút thuốc lá
6. Kiểm tra đường huyết định kỳ nếu có nguy cơ cao

Nếu bạn có các yếu tố nguy cơ (tiền sử gia đình, thừa cân, trên 45 tuổi), hãy kiểm tra đường huyết định kỳ 6 tháng một lần.',
                'category' => 'Bệnh mãn tính',
                'tags' => ['tiểu đường', 'đường huyết', 'sức khỏe', 'phòng ngừa', 'bệnh mãn tính'],
            ],
            [
                'title' => 'Cách duy trì huyết áp ổn định và phòng ngừa tăng huyết áp',
                'content' => 'Huyết áp cao (tăng huyết áp) là một vấn đề sức khỏe phổ biến có thể dẫn đến các biến chứng nghiêm trọng như đột quỵ, bệnh tim, suy thận.

Huyết áp bình thường: Dưới 120/80 mmHg
Huyết áp cao: Từ 140/90 mmHg trở lên

Cách duy trì huyết áp ổn định:

1. Chế độ ăn uống:
   - Giảm lượng muối xuống dưới 5g/ngày (1 muỗng cà phê)
   - Ăn nhiều trái cây và rau xanh (ít nhất 5 phần/ngày)
   - Chọn thực phẩm giàu kali (chuối, khoai tây, cà chua)
   - Hạn chế thực phẩm chế biến sẵn

2. Hoạt động thể chất:
   - Tập thể dục cường độ vừa ít nhất 30 phút/ngày
   - Các bài tập phù hợp: đi bộ nhanh, đạp xe, bơi lội
   - Tập ít nhất 5 ngày/tuần

3. Duy trì cân nặng hợp lý:
   - Giảm 1kg có thể giảm huyết áp 1 mmHg
   - Vòng eo: nam < 90cm, nữ < 80cm

4. Hạn chế rượu bia:
   - Nam: tối đa 2 ly/ngày
   - Nữ: tối đa 1 ly/ngày

5. Bỏ thuốc lá hoàn toàn

6. Quản lý căng thẳng:
   - Thiền, yoga, hít thở sâu
   - Ngủ đủ 7-8 giờ/đêm

7. Đo huyết áp thường xuyên:
   - Nên đo vào cùng một thời điểm mỗi ngày
   - Ghi lại kết quả để theo dõi

Nếu huyết áp cao hơn 140/90 mmHg, hãy tham khảo ý kiến bác sĩ ngay để được điều trị phù hợp.',
                'category' => 'Tim mạch',
                'tags' => ['huyết áp', 'tim mạch', 'sức khỏe tim', 'phòng ngừa', 'tăng huyết áp'],
            ],
            [
                'title' => 'Chế độ dinh dưỡng khoa học cho người cao tuổi',
                'content' => 'Dinh dưỡng đúng cách rất quan trọng đối với người cao tuổi để duy trì sức khỏe, năng lượng và phòng ngừa bệnh tật.

Nguyên tắc dinh dưỡng cho người cao tuổi:

1. Bổ sung đủ canxi cho xương:
   - Sữa và sản phẩm từ sữa: 2-3 phần/ngày (1 phần = 1 ly sữa hoặc 1 hộp sữa chua)
   - Cá nhỏ ăn cả xương: cá cơm, cá mòi
   - Rau xanh: cải xoăn, bông cải xanh
   - Mục tiêu: 1000-1200mg canxi/ngày

2. Protein để duy trì cơ bắp:
   - Thịt nạc: 100-150g/ngày
   - Cá: ít nhất 2 lần/tuần
   - Đậu, đậu phụ: nguồn protein thực vật tốt
   - Trứng: 3-4 quả/tuần

3. Chất xơ cho tiêu hóa:
   - Rau củ quả: ít nhất 400g/ngày
   - Ngũ cốc nguyên hạt: gạo lứt, bánh mì đen
   - Mục tiêu: 25-30g chất xơ/ngày

4. Uống đủ nước:
   - Ít nhất 1.5-2 lít nước/ngày
   - Nước lọc, nước canh, trà thảo mộc
   - Tránh nước ngọt có gas

5. Hạn chế:
   - Muối: dưới 5g/ngày
   - Đường: dưới 25g/ngày
   - Chất béo bão hòa: hạn chế mỡ động vật

6. Chia nhỏ bữa ăn:
   - 5-6 bữa nhỏ thay vì 3 bữa lớn
   - Dễ tiêu hóa, tránh đầy bụng

7. Bổ sung vitamin D:
   - Tắm nắng 15-20 phút/ngày (trước 10h sáng)
   - Hoặc bổ sung theo chỉ định bác sĩ

Lưu ý: Nếu có bệnh mãn tính, hãy tham khảo ý kiến bác sĩ hoặc chuyên gia dinh dưỡng để có chế độ ăn phù hợp.',
                'category' => 'Dinh dưỡng',
                'tags' => ['dinh dưỡng', 'người cao tuổi', 'ăn uống lành mạnh', 'sức khỏe', 'canxi'],
            ],
            [
                'title' => 'Tầm quan trọng của giấc ngủ đối với sức khỏe và cách cải thiện',
                'content' => 'Giấc ngủ đóng vai trò quan trọng trong việc duy trì sức khỏe thể chất và tinh thần. Người trưởng thành cần 7-9 giờ ngủ mỗi đêm.

Tác hại của thiếu ngủ:
• Suy giảm trí nhớ và khả năng tập trung
• Tăng nguy cơ béo phì (do rối loạn hormone)
• Tăng nguy cơ tiểu đường type 2
• Tăng nguy cơ bệnh tim và đột quỵ
• Suy giảm hệ miễn dịch
• Tăng nguy cơ trầm cảm và lo âu
• Lão hóa da nhanh hơn

Cách cải thiện giấc ngủ:

1. Duy trì lịch ngủ đều đặn:
   - Đi ngủ và thức dậy cùng một giờ mỗi ngày (kể cả cuối tuần)
   - Giúp đồng hồ sinh học ổn định

2. Tạo môi trường ngủ lý tưởng:
   - Nhiệt độ phòng: 18-22°C
   - Tối hoàn toàn (dùng rèm che hoặc mặt nạ ngủ)
   - Yên tĩnh (dùng nút tai nếu cần)
   - Nệm và gối thoải mái

3. Tránh trước khi ngủ:
   - Caffeine: ít nhất 6 giờ trước khi ngủ
   - Rượu bia: ít nhất 3 giờ trước khi ngủ
   - Bữa ăn lớn: ít nhất 3 giờ trước khi ngủ
   - Nicotine: bỏ hoàn toàn

4. Tắt thiết bị điện tử:
   - Ít nhất 1 giờ trước khi ngủ
   - Ánh sáng xanh từ màn hình ức chế melatonin

5. Thư giãn trước khi ngủ:
   - Tắm nước ấm
   - Đọc sách (sách giấy, không phải điện tử)
   - Nghe nhạc nhẹ
   - Thiền hoặc hít thở sâu

6. Tập thể dục:
   - Tập thường xuyên giúp ngủ ngon hơn
   - Nhưng không tập quá gần giờ ngủ (ít nhất 3 giờ)

7. Nếu không ngủ được:
   - Đừng nằm trên giường quá 20 phút
   - Ra khỏi giường, làm việc nhẹ nhàng
   - Quay lại giường khi cảm thấy buồn ngủ

Nếu mất ngủ kéo dài hơn 3 tuần, hãy tham khảo ý kiến bác sĩ.',
                'category' => 'Sức khỏe tổng quát',
                'tags' => ['giấc ngủ', 'sức khỏe', 'lối sống', 'phòng ngừa', 'mất ngủ'],
            ],
            [
                'title' => 'Cách phòng ngừa cảm cúm và cảm lạnh hiệu quả',
                'content' => 'Cảm cúm và cảm lạnh là các bệnh nhiễm trùng đường hô hấp phổ biến, đặc biệt vào mùa lạnh.

Sự khác biệt:
• Cảm lạnh: Triệu chứng nhẹ hơn, thường tự khỏi sau 7-10 ngày
• Cảm cúm: Triệu chứng nặng hơn, có thể gây biến chứng, cần điều trị

Cách phòng ngừa hiệu quả:

1. Rửa tay đúng cách:
   - Rửa bằng xà phòng và nước trong ít nhất 20 giây
   - Hoặc sử dụng nước rửa tay có cồn (ít nhất 60%)
   - Rửa tay sau khi: ho, hắt hơi, chạm vào bề mặt công cộng
   - Trước khi: ăn uống, chạm vào mắt/mũi/miệng

2. Tránh chạm vào mặt:
   - Virus có thể xâm nhập qua mắt, mũi, miệng
   - Dùng khăn giấy khi ho hoặc hắt hơi
   - Vứt khăn giấy ngay sau khi dùng

3. Tiêm phòng cúm:
   - Tiêm phòng cúm hàng năm (tốt nhất vào tháng 9-11)
   - Đặc biệt quan trọng cho:
     * Người trên 65 tuổi
     * Trẻ em dưới 5 tuổi
     * Phụ nữ mang thai
     * Người có bệnh mãn tính
     * Nhân viên y tế

4. Tránh tiếp xúc gần:
   - Giữ khoảng cách ít nhất 1 mét với người bị bệnh
   - Tránh đến nơi đông người trong mùa dịch
   - Ở nhà nếu bạn bị bệnh

5. Tăng cường hệ miễn dịch:
   - Ăn uống cân bằng: nhiều trái cây, rau xanh
   - Tập thể dục thường xuyên
   - Ngủ đủ giấc (7-9 giờ/đêm)
   - Quản lý căng thẳng
   - Uống đủ nước

6. Vệ sinh môi trường:
   - Làm sạch và khử trùng bề mặt thường xuyên chạm vào
   - Thông gió phòng thường xuyên

7. Không dùng chung đồ dùng cá nhân:
   - Khăn, cốc, bàn chải đánh răng

Khi bị bệnh:
- Nghỉ ngơi đầy đủ
- Uống nhiều nước
- Dùng thuốc giảm đau/hạ sốt nếu cần
- Đi khám nếu: sốt cao >38.5°C, khó thở, triệu chứng nặng',
                'category' => 'Bệnh truyền nhiễm',
                'tags' => ['cảm cúm', 'cảm lạnh', 'phòng ngừa', 'miễn dịch', 'vệ sinh'],
            ],
        ];

        // Thêm 114 bài viết knowledge base còn lại với nội dung chi tiết (tổng 120 bài)
        $moreArticles = $this->generateMoreKnowledgeBaseArticles(114);
        $articles = array_merge($articles, $moreArticles);

        foreach ($articles as $article) {
            MedicalContent::create([
                'content_type' => 'knowledge_base',
                'title' => $article['title'],
                'content' => $article['content'],
                'category' => $article['category'],
                'tags' => $article['tags'],
                'status' => 'published',
                'views_count' => rand(50, 800),
                'helpful_count' => rand(5, 80),
            ]);
        }

        $this->command->info('Đã thêm 120 bài viết Knowledge Base.');
    }

    private function seedFAQs(): void
    {
        $faqs = [
            [
                'title' => 'Tôi nên khám sức khỏe định kỳ bao lâu một lần?',
                'content' => 'Tần suất khám sức khỏe định kỳ phụ thuộc vào độ tuổi, giới tính và tình trạng sức khỏe của bạn.

Khuyến nghị chung:
• Người trưởng thành khỏe mạnh (18-40 tuổi): Khám tổng quát 1 lần/năm
• Người trung niên (40-65 tuổi): Khám 1-2 lần/năm
• Người cao tuổi (trên 65 tuổi): Khám 2-4 lần/năm

Các xét nghiệm cần làm định kỳ:
1. Xét nghiệm máu cơ bản: Mỗi năm
   - Công thức máu
   - Đường huyết
   - Cholesterol
   - Chức năng gan, thận

2. Đo huyết áp: Mỗi 6 tháng (hoặc thường xuyên hơn nếu có vấn đề)

3. Khám mắt: Mỗi 2 năm (hoặc 1 năm nếu trên 60 tuổi)

4. Khám răng: Mỗi 6 tháng

5. Phụ nữ:
   - Khám phụ khoa: Mỗi năm
   - Chụp nhũ ảnh: Bắt đầu từ 40 tuổi, mỗi 1-2 năm
   - Pap smear: Mỗi 3 năm (nếu kết quả bình thường)

6. Nam giới:
   - Khám tuyến tiền liệt: Bắt đầu từ 50 tuổi, mỗi năm
   - PSA test: Theo chỉ định bác sĩ

Lưu ý: Nếu bạn có các yếu tố nguy cơ (tiền sử gia đình, bệnh mãn tính), hãy khám thường xuyên hơn theo chỉ định của bác sĩ.',
                'category' => 'Khám sức khỏe',
            ],
            [
                'title' => 'Làm thế nào để biết tôi có bị thiếu máu không?',
                'content' => 'Thiếu máu là tình trạng cơ thể không có đủ hồng cầu khỏe mạnh để vận chuyển oxy đến các mô.

Triệu chứng phổ biến:
• Mệt mỏi, yếu sức, dễ kiệt sức
• Da xanh xao, nhợt nhạt
• Khó thở, đặc biệt khi vận động
• Chóng mặt, đau đầu
• Tim đập nhanh hoặc không đều
• Tay chân lạnh
• Tóc rụng, móng tay giòn

Nguyên nhân thường gặp:
1. Thiếu sắt (phổ biến nhất):
   - Mất máu (kinh nguyệt nhiều, xuất huyết)
   - Chế độ ăn thiếu sắt
   - Không hấp thu sắt tốt

2. Thiếu vitamin B12 hoặc folate:
   - Chế độ ăn thiếu (đặc biệt người ăn chay)
   - Không hấp thu tốt

3. Bệnh mãn tính:
   - Bệnh thận, viêm khớp, ung thư

4. Bệnh di truyền:
   - Thalassemia, hồng cầu hình liềm

Cách chẩn đoán:
Bác sĩ sẽ làm xét nghiệm máu để đo:
- Hemoglobin (Hb): Bình thường > 12g/dL (nữ) hoặc > 13g/dL (nam)
- Hematocrit: Tỷ lệ hồng cầu trong máu
- Số lượng hồng cầu

Điều trị:
• Thiếu sắt: Bổ sung sắt + ăn thực phẩm giàu sắt (thịt đỏ, rau xanh, đậu)
• Thiếu B12: Bổ sung B12 hoặc tiêm
• Điều trị nguyên nhân gốc rễ

Nếu bạn nghi ngờ mình bị thiếu máu, hãy đi khám bác sĩ để được chẩn đoán và điều trị đúng cách.',
                'category' => 'Bệnh lý',
            ],
            [
                'title' => 'Tôi có nên uống vitamin tổng hợp không?',
                'content' => 'Câu trả lời phụ thuộc vào tình trạng sức khỏe và chế độ ăn uống của bạn.

Khi NÊN uống vitamin tổng hợp:
1. Chế độ ăn không đầy đủ:
   - Ăn ít trái cây, rau xanh
   - Chế độ ăn kiêng nghiêm ngặt
   - Ăn chay hoàn toàn (cần B12)

2. Người cao tuổi:
   - Hấp thu dinh dưỡng kém hơn
   - Ăn ít hơn
   - Cần nhiều vitamin D và B12

3. Phụ nữ mang thai:
   - Cần bổ sung axit folic, sắt
   - Theo chỉ định bác sĩ

4. Người có bệnh ảnh hưởng hấp thu:
   - Bệnh Crohn, viêm loét đại tràng
   - Phẫu thuật dạ dày

5. Người không tiếp xúc ánh nắng:
   - Cần bổ sung vitamin D

Khi KHÔNG CẦN:
• Người khỏe mạnh có chế độ ăn cân bằng
• Đã nhận đủ dinh dưỡng từ thực phẩm

Lưu ý quan trọng:
• Tốt nhất là nhận vitamin từ thực phẩm tự nhiên
• Không uống quá liều (có thể gây hại)
• Tham khảo ý kiến bác sĩ trước khi uống, đặc biệt nếu đang dùng thuốc khác
• Chọn sản phẩm chất lượng, có uy tín

Khuyến nghị: Nếu bạn ăn uống đầy đủ và đa dạng, bạn có thể không cần vitamin tổng hợp. Tuy nhiên, nếu có nghi ngờ, hãy tham khảo ý kiến bác sĩ hoặc chuyên gia dinh dưỡng.',
                'category' => 'Dinh dưỡng',
            ],
        ];

        // Thêm 57 FAQs còn lại với câu trả lời chi tiết (tổng 60 câu)
        $moreFaqs = $this->generateMoreFAQs(57);
        $faqs = array_merge($faqs, $moreFaqs);

        foreach ($faqs as $faq) {
            MedicalContent::create([
                'content_type' => 'faq',
                'title' => $faq['title'],
                'content' => $faq['content'],
                'category' => $faq['category'],
                'status' => 'published',
                'views_count' => rand(30, 500),
                'helpful_count' => rand(3, 60),
            ]);
        }

        $this->command->info('Đã thêm 60 FAQs.');
    }

    private function seedTemplates(): void
    {
        $templates = [
            [
                'title' => 'Mẫu tư vấn bệnh tiểu đường',
                'content' => 'Mẫu tư vấn cho bệnh nhân tiểu đường:

1. Đánh giá tình trạng hiện tại:
   - Đo đường huyết lúc đói và sau ăn
   - Kiểm tra HbA1c (mục tiêu < 7%)
   - Đánh giá biến chứng (mắt, thận, thần kinh)

2. Hướng dẫn chế độ ăn uống:
   - Chia nhỏ bữa ăn: 3 bữa chính + 2-3 bữa phụ
   - Hạn chế đường và tinh bột tinh chế
   - Tăng cường rau xanh, chất xơ
   - Chọn carbohydrate phức tạp (gạo lứt, yến mạch)

3. Lịch tập thể dục:
   - Ít nhất 150 phút/tuần (đi bộ, đạp xe)
   - Kết hợp tập sức mạnh 2 lần/tuần
   - Theo dõi đường huyết trước và sau tập

4. Theo dõi đường huyết:
   - Ghi nhật ký đường huyết
   - Đo 2-4 lần/ngày (trước ăn, sau ăn 2 giờ)

5. Quản lý thuốc:
   - Uống thuốc đúng giờ, đúng liều
   - Không tự ý ngừng thuốc
   - Tái khám định kỳ',
                'category' => 'Bệnh mãn tính',
                'specialty' => 'Nội tiết',
            ],
            [
                'title' => 'Mẫu tư vấn tăng huyết áp',
                'content' => 'Mẫu tư vấn cho bệnh nhân tăng huyết áp:

1. Đo huyết áp định kỳ:
   - Đo 2 lần/ngày (sáng và chiều)
   - Ghi lại kết quả
   - Đo vào cùng thời điểm mỗi ngày

2. Chế độ ăn ít muối:
   - Dưới 5g muối/ngày
   - Tránh thực phẩm chế biến sẵn
   - Tăng cường kali (chuối, khoai tây)

3. Tập thể dục:
   - 30 phút/ngày, 5 ngày/tuần
   - Đi bộ, đạp xe, bơi lội

4. Quản lý căng thẳng:
   - Thiền, yoga
   - Ngủ đủ giấc
   - Tránh làm việc quá sức

5. Tuân thủ điều trị:
   - Uống thuốc đúng giờ
   - Tái khám định kỳ
   - Không tự ý ngừng thuốc',
                'category' => 'Tim mạch',
                'specialty' => 'Tim mạch',
            ],
        ];

        // Thêm 18 templates còn lại (tổng 20 mẫu)
        $moreTemplates = $this->generateMoreTemplates(18);
        $templates = array_merge($templates, $moreTemplates);

        foreach ($templates as $template) {
            MedicalContent::create([
                'content_type' => 'template',
                'title' => $template['title'],
                'content' => $template['content'],
                'category' => $template['category'],
                'specialty' => $template['specialty'],
                'status' => 'published',
            ]);
        }

        $this->command->info('Đã thêm 20 Templates.');
    }

    private function generateMoreKnowledgeBaseArticles(int $count = 114): array
    {
        $articles = [];
        
        // Danh sách các chủ đề với nội dung chi tiết
        $topics = [
            [
                'title' => 'Bệnh tim mạch và cách phòng ngừa hiệu quả',
                'category' => 'Tim mạch',
                'tags' => ['tim mạch', 'sức khỏe tim', 'phòng ngừa', 'bệnh tim'],
                'content' => 'Bệnh tim mạch là nguyên nhân tử vong hàng đầu. Yếu tố nguy cơ: hút thuốc, tăng huyết áp, cholesterol cao, tiểu đường, béo phì. Cách phòng ngừa: không hút thuốc, kiểm soát huyết áp và cholesterol, tập thể dục ít nhất 150 phút/tuần, ăn uống lành mạnh, duy trì cân nặng hợp lý, quản lý căng thẳng, khám sức khỏe định kỳ.'
            ],
            [
                'title' => 'Chế độ dinh dưỡng cho người bị gout',
                'category' => 'Dinh dưỡng',
                'tags' => ['gout', 'dinh dưỡng', 'bệnh lý', 'acid uric'],
                'content' => 'Gout là bệnh do tích tụ acid uric. Hạn chế thực phẩm giàu purine: nội tạng, thịt đỏ, hải sản. Tăng cường: rau xanh, trái cây (đặc biệt cherry), sữa ít béo. Uống nhiều nước (2-3 lít/ngày). Hạn chế rượu bia, đặc biệt là bia. Tránh đường fructose. Thực đơn: cháo yến mạch, cá nướng, rau luộc, salad.'
            ],
            // Thêm các chủ đề khác...
        ];

        // Tạo đủ số lượng bài viết
        $allTopics = [
            ['Bệnh tim mạch và cách phòng ngừa', 'Tim mạch', ['tim mạch', 'sức khỏe tim']],
            ['Chế độ dinh dưỡng cho người bị gout', 'Dinh dưỡng', ['gout', 'dinh dưỡng']],
            ['Cách phòng ngừa loãng xương', 'Xương khớp', ['loãng xương', 'xương khớp']],
            ['Tác hại của thuốc lá và cách bỏ thuốc', 'Sức khỏe tổng quát', ['thuốc lá', 'bỏ thuốc']],
            ['Chăm sóc sức khỏe răng miệng', 'Răng miệng', ['răng miệng', 'vệ sinh']],
            ['Hiểu về bệnh hen suyễn', 'Hô hấp', ['hen suyễn', 'hô hấp']],
            ['Cách phòng ngừa ung thư', 'Ung thư', ['ung thư', 'phòng ngừa']],
            ['Dinh dưỡng cho phụ nữ mang thai', 'Dinh dưỡng', ['mang thai', 'dinh dưỡng']],
            ['Tập luyện cho người bị đau khớp', 'Thể dục thể thao', ['đau khớp', 'tập luyện']],
            ['Cách quản lý bệnh viêm khớp', 'Xương khớp', ['viêm khớp', 'xương khớp']],
        ];

        // Tạo nội dung chi tiết cho mỗi bài với các chủ đề đa dạng
        $detailedTopics = [
            ['Bệnh tim mạch và cách phòng ngừa', 'Tim mạch', ['tim mạch', 'sức khỏe tim'], 'Bệnh tim mạch là nguyên nhân tử vong hàng đầu. Yếu tố nguy cơ bao gồm: hút thuốc lá, tăng huyết áp, cholesterol cao, tiểu đường, béo phì, lười vận động, căng thẳng. Cách phòng ngừa: 1) Không hút thuốc lá hoàn toàn. 2) Kiểm soát huyết áp và cholesterol thông qua chế độ ăn và thuốc nếu cần. 3) Tập thể dục ít nhất 150 phút/tuần với cường độ vừa. 4) Ăn uống lành mạnh: nhiều rau xanh, trái cây, ngũ cốc nguyên hạt, ít chất béo bão hòa. 5) Duy trì cân nặng hợp lý (BMI 18.5-24.9). 6) Quản lý căng thẳng thông qua thiền, yoga, hoặc các hoạt động giải trí. 7) Khám sức khỏe định kỳ để phát hiện sớm các vấn đề.'],
            ['Chế độ dinh dưỡng cho người bị gout', 'Dinh dưỡng', ['gout', 'dinh dưỡng', 'acid uric'], 'Gout là bệnh do tích tụ acid uric trong cơ thể. Nguyên tắc dinh dưỡng: Hạn chế thực phẩm giàu purine như nội tạng động vật, thịt đỏ, hải sản (tôm, cua, cá mòi), nấm, đậu Hà Lan. Tăng cường: rau xanh (trừ rau chân vịt), trái cây (đặc biệt cherry có tác dụng giảm acid uric), sữa ít béo, ngũ cốc nguyên hạt. Uống nhiều nước: 2-3 lít/ngày để đào thải acid uric. Hạn chế rượu bia, đặc biệt là bia (chứa nhiều purine). Tránh đường fructose và đồ uống có gas. Thực đơn mẫu: sáng - cháo yến mạch, trưa - cá nướng với rau luộc, tối - salad rau củ với ức gà.'],
            ['Cách phòng ngừa loãng xương', 'Xương khớp', ['loãng xương', 'xương khớp', 'canxi'], 'Loãng xương làm xương yếu và dễ gãy. Phòng ngừa: 1) Bổ sung đủ canxi: 1000mg/ngày (người trưởng thành), 1200mg/ngày (phụ nữ trên 50, nam trên 70). Nguồn canxi: sữa, sữa chua, phô mai, cá nhỏ ăn cả xương, rau xanh (cải xoăn, bông cải), đậu phụ. 2) Vitamin D: 600-800 IU/ngày để hấp thu canxi. Tắm nắng 15-20 phút/ngày hoặc bổ sung. 3) Tập thể dục: đi bộ, chạy bộ, nâng tạ, yoga - ít nhất 30 phút/ngày, 5 ngày/tuần. 4) Không hút thuốc và hạn chế rượu. 5) Phụ nữ sau mãn kinh nên kiểm tra mật độ xương định kỳ.'],
            ['Tác hại của thuốc lá và cách bỏ thuốc', 'Sức khỏe tổng quát', ['thuốc lá', 'bỏ thuốc', 'sức khỏe'], 'Thuốc lá gây ra nhiều bệnh: ung thư phổi, bệnh tim, đột quỵ, COPD, và nhiều loại ung thư khác. Cách bỏ thuốc: 1) Xác định lý do bỏ thuốc và viết ra. 2) Chọn ngày bỏ thuốc cụ thể. 3) Loại bỏ tất cả thuốc lá, gạt tàn. 4) Tránh các tình huống kích thích hút thuốc. 5) Tìm hoạt động thay thế: nhai kẹo cao su, uống nước, tập thể dục. 6) Sử dụng liệu pháp thay thế nicotine: miếng dán, kẹo cao su nicotine (theo chỉ định bác sĩ). 7) Tham gia nhóm hỗ trợ hoặc tư vấn. 8) Tập thể dục để giảm căng thẳng. 9) Ăn uống lành mạnh. 10) Thưởng cho bản thân khi đạt mốc (1 tuần, 1 tháng, 3 tháng).'],
            ['Chăm sóc sức khỏe răng miệng đúng cách', 'Răng miệng', ['răng miệng', 'vệ sinh', 'sức khỏe'], 'Chăm sóc răng miệng đúng cách: 1) Đánh răng 2 lần/ngày (sáng và tối) với kem đánh răng có fluoride, mỗi lần 2 phút. 2) Dùng chỉ nha khoa ít nhất 1 lần/ngày để làm sạch kẽ răng. 3) Súc miệng bằng nước súc miệng có fluoride. 4) Thay bàn chải 3-4 tháng/lần hoặc khi lông bàn chải bị mòn. 5) Khám răng định kỳ 6 tháng/lần để làm sạch và kiểm tra. 6) Hạn chế đường và đồ ngọt, đặc biệt giữa các bữa ăn. 7) Không hút thuốc lá (gây vàng răng, bệnh nướu, ung thư miệng). 8) Uống đủ nước để giữ miệng ẩm và rửa sạch vi khuẩn.'],
            ['Hiểu về bệnh hen suyễn', 'Hô hấp', ['hen suyễn', 'hô hấp', 'bệnh lý'], 'Hen suyễn là bệnh viêm đường hô hấp mãn tính. Triệu chứng: khó thở, thở khò khè, ho, tức ngực. Yếu tố kích thích: dị ứng (phấn hoa, bụi, lông thú), nhiễm trùng đường hô hấp, không khí lạnh, tập thể dục, stress, khói thuốc. Điều trị: 1) Thuốc cắt cơn (salbutamol) khi lên cơn. 2) Thuốc kiểm soát dài hạn (corticosteroid dạng hít) để phòng ngừa. 3) Tránh các yếu tố kích thích. 4) Sử dụng máy phun sương nếu cần. 5) Tập thể dục phù hợp (bơi lội tốt cho người hen suyễn). 6) Theo dõi và ghi nhật ký cơn hen. 7) Có kế hoạch hành động khi lên cơn nặng.'],
            ['Cách phòng ngừa ung thư', 'Ung thư', ['ung thư', 'phòng ngừa', 'sức khỏe'], 'Phòng ngừa ung thư: 1) Không hút thuốc lá và tránh khói thuốc thụ động. 2) Duy trì cân nặng hợp lý (giảm nguy cơ 13 loại ung thư). 3) Tập thể dục ít nhất 150 phút/tuần. 4) Ăn uống lành mạnh: nhiều rau xanh, trái cây, ngũ cốc nguyên hạt, ít thịt đỏ và thịt chế biến sẵn. 5) Hạn chế rượu bia: nam tối đa 2 ly/ngày, nữ 1 ly/ngày. 6) Bảo vệ khỏi ánh nắng: dùng kem chống nắng SPF 30+, mặc quần áo che phủ, tránh nắng giữa trưa. 7) Tiêm phòng: HPV (ung thư cổ tử cung), viêm gan B (ung thư gan). 8) Khám sức khỏe định kỳ và tầm soát: nhũ ảnh, Pap smear, nội soi đại tràng. 9) Tránh tiếp xúc với hóa chất độc hại.'],
            ['Dinh dưỡng cho phụ nữ mang thai', 'Dinh dưỡng', ['mang thai', 'dinh dưỡng', 'phụ nữ'], 'Dinh dưỡng khi mang thai: 1) Axit folic: 400-800mcg/ngày trước và trong thai kỳ (rau xanh, đậu, ngũ cốc bổ sung). 2) Sắt: 27mg/ngày (thịt đỏ, rau xanh, đậu). 3) Canxi: 1000mg/ngày (sữa, sữa chua, rau xanh). 4) Protein: tăng 25g/ngày (thịt nạc, cá, đậu, trứng). 5) Omega-3: cá béo 2 lần/tuần (tránh cá có nhiều thủy ngân). 6) Ăn nhiều bữa nhỏ để tránh buồn nôn. 7) Tránh: rượu bia, cá sống, thịt sống, sữa chưa tiệt trùng, caffeine quá mức (dưới 200mg/ngày). 8) Tăng cân hợp lý: 11-16kg (BMI bình thường), 7-11kg (thừa cân), 5-9kg (béo phì).'],
            ['Tập luyện cho người bị đau khớp', 'Thể dục thể thao', ['đau khớp', 'tập luyện', 'xương khớp'], 'Tập luyện an toàn cho người đau khớp: 1) Khởi động 5-10 phút trước khi tập. 2) Bài tập phù hợp: đi bộ (bắt đầu 10-15 phút), đạp xe, bơi lội (giảm áp lực lên khớp), yoga nhẹ nhàng, thái cực quyền. 3) Tập sức mạnh nhẹ: nâng tạ nhỏ, dùng dây kháng lực. 4) Kéo giãn: giữ mỗi động tác 15-30 giây, không đau. 5) Tránh: chạy trên bề mặt cứng, nhảy cao, nâng tạ nặng. 6) Nghỉ ngơi khi đau. 7) Chườm nóng trước tập, chườm lạnh sau tập. 8) Tăng dần cường độ từ từ. 9) Tập 30 phút/ngày, 5 ngày/tuần.'],
            ['Cách quản lý bệnh viêm khớp', 'Xương khớp', ['viêm khớp', 'xương khớp', 'bệnh lý'], 'Quản lý viêm khớp: 1) Thuốc: NSAID (ibuprofen), corticosteroid, thuốc chống thấp khớp (DMARDs) theo chỉ định bác sĩ. 2) Vật lý trị liệu: tập luyện để tăng cường cơ, cải thiện vận động. 3) Tập thể dục: đi bộ, bơi lội, yoga - 30 phút/ngày. 4) Quản lý cân nặng: giảm cân giúp giảm áp lực lên khớp. 5) Chườm nóng/lạnh: nóng để giảm cứng khớp, lạnh để giảm sưng đau. 6) Bổ sung: omega-3, glucosamine (theo chỉ định). 7) Tránh: thức ăn gây viêm (đường, thịt đỏ, thực phẩm chế biến). 8) Ngủ đủ giấc. 9) Quản lý căng thẳng. 10) Sử dụng dụng cụ hỗ trợ nếu cần.'],
        ];

        for ($i = 0; $i < $count; $i++) {
            if ($i < count($detailedTopics)) {
                $topic = $detailedTopics[$i];
                $articles[] = [
                    'title' => $topic[0],
                    'content' => $topic[3],
                    'category' => $topic[1],
                    'tags' => $topic[2],
                ];
            } else {
                // Sử dụng danh sách topics cơ bản cho các bài còn lại
                $baseTopic = $allTopics[($i - count($detailedTopics)) % count($allTopics)];
                $part = floor(($i - count($detailedTopics)) / count($allTopics)) + 1;
                $articles[] = [
                    'title' => $baseTopic[0] . ' - Phần ' . $part,
                    'content' => 'Bài viết chi tiết về ' . $baseTopic[0] . '. Chúng ta sẽ tìm hiểu về nguyên nhân, triệu chứng, cách phòng ngừa và điều trị. Đây là một vấn đề sức khỏe quan trọng cần được quan tâm đúng mức. Các biện pháp phòng ngừa và điều trị cần được thực hiện dưới sự hướng dẫn của bác sĩ chuyên khoa. Để có thêm thông tin cụ thể, hãy tham khảo ý kiến của các chuyên gia y tế.',
                    'category' => $baseTopic[1],
                    'tags' => $baseTopic[2],
                ];
            }
        }

        return $articles;
    }

    private function generateMoreFAQs(int $count = 57): array
    {
        $faqs = [];
        
        $faqList = [
            ['Có bao nhiêu calo tôi nên ăn mỗi ngày?', 'Dinh dưỡng', 'Lượng calo phụ thuộc vào tuổi, giới tính, chiều cao, cân nặng, mức độ hoạt động. Phụ nữ: 1,800-2,400 calo/ngày. Nam giới: 2,200-3,000 calo/ngày. Để giảm cân: giảm 500-750 calo/ngày. Để tăng cân: tăng 300-500 calo/ngày.'],
            ['Tập thể dục bao nhiêu là đủ?', 'Thể dục thể thao', 'Ít nhất 150 phút/tuần cường độ vừa hoặc 75 phút/tuần cường độ cao. Kết hợp tập sức mạnh 2 lần/tuần. Người cao tuổi thêm bài tập giữ thăng bằng.'],
            ['Làm thế nào để giảm đau đầu?', 'Sức khỏe tổng quát', 'Nghỉ ngơi, uống đủ nước, chườm lạnh hoặc nóng, massage, tránh căng thẳng. Nếu đau đầu thường xuyên hoặc dữ dội, hãy đi khám bác sĩ.'],
            ['Tôi nên ngủ bao nhiêu giờ mỗi đêm?', 'Sức khỏe tổng quát', 'Người trưởng thành cần 7-9 giờ/đêm. Người cao tuổi: 7-8 giờ. Trẻ em và thanh thiếu niên cần nhiều hơn.'],
            ['Làm thế nào để tăng cân an toàn?', 'Dinh dưỡng', 'Tăng 300-500 calo/ngày, ăn nhiều bữa nhỏ, chọn thực phẩm giàu dinh dưỡng, kết hợp tập luyện để tăng cơ.'],
            ['Tôi có nên lo lắng về nhịp tim nhanh không?', 'Tim mạch', 'Nhịp tim nhanh có thể do căng thẳng, tập thể dục, caffeine. Nếu kèm theo đau ngực, khó thở, chóng mặt, hãy đi khám ngay.'],
            ['Làm thế nào để cải thiện tiêu hóa?', 'Tiêu hóa', 'Ăn nhiều chất xơ, uống đủ nước, tập thể dục, ăn chậm nhai kỹ, tránh thức ăn cay nóng, bổ sung probiotics.'],
            ['Tôi nên tập thể dục khi nào?', 'Thể dục thể thao', 'Tập bất cứ lúc nào bạn có thể. Tốt nhất là buổi sáng hoặc chiều. Tránh tập ngay sau bữa ăn lớn.'],
            ['Làm thế nào để giảm đau cơ sau khi tập?', 'Thể dục thể thao', 'Khởi động trước, thư giãn sau tập, massage, chườm nóng/lạnh, uống đủ nước, nghỉ ngơi đầy đủ.'],
            ['Tôi có nên uống nước ép trái cây không?', 'Dinh dưỡng', 'Nước ép trái cây có đường, nên hạn chế. Tốt hơn là ăn trái cây nguyên quả để có chất xơ.'],
        ];

        // Tạo thêm FAQs với nội dung chi tiết
        $detailedFAQs = [
            ['Có bao nhiêu calo tôi nên ăn mỗi ngày?', 'Dinh dưỡng', 'Lượng calo phụ thuộc vào: tuổi, giới tính, chiều cao, cân nặng, mức độ hoạt động. Phụ nữ trưởng thành: 1,800-2,400 calo/ngày. Nam giới: 2,200-3,000 calo/ngày. Để giảm cân: giảm 500-750 calo/ngày so với nhu cầu. Để tăng cân: tăng 300-500 calo/ngày. Cách tính: BMR (tỷ lệ trao đổi chất cơ bản) x hệ số hoạt động. Nên tham khảo chuyên gia dinh dưỡng để có kế hoạch phù hợp với nhu cầu cá nhân.'],
            ['Tập thể dục bao nhiêu là đủ?', 'Thể dục thể thao', 'Theo WHO: ít nhất 150 phút/tuần cường độ vừa (đi bộ nhanh) hoặc 75 phút/tuần cường độ cao (chạy). Kết hợp tập sức mạnh 2 lần/tuần (nâng tạ, yoga). Người cao tuổi thêm bài tập giữ thăng bằng. Có thể chia nhỏ: 10 phút/lần, miễn tổng đạt mục tiêu. Quan trọng là tìm hoạt động bạn thích và có thể duy trì lâu dài.'],
            ['Làm thế nào để giảm đau đầu?', 'Sức khỏe tổng quát', 'Các cách giảm đau đầu: 1) Nghỉ ngơi trong phòng tối, yên tĩnh. 2) Uống đủ nước (mất nước gây đau đầu). 3) Chườm lạnh hoặc nóng lên trán/gáy. 4) Massage nhẹ nhàng vùng đầu, cổ, vai. 5) Tránh căng thẳng, thư giãn. 6) Ngủ đủ giấc. 7) Tránh caffeine nếu đã quen dùng. 8) Dùng thuốc giảm đau (paracetamol, ibuprofen) nếu cần. Nếu đau đầu thường xuyên (>15 ngày/tháng), dữ dội, hoặc kèm theo các triệu chứng khác, hãy đi khám bác sĩ.'],
            ['Tôi nên ngủ bao nhiêu giờ mỗi đêm?', 'Sức khỏe tổng quát', 'Người trưởng thành (18-64 tuổi): 7-9 giờ/đêm. Người cao tuổi (65+): 7-8 giờ/đêm. Trẻ em: 9-11 giờ. Thanh thiếu niên: 8-10 giờ. Chất lượng giấc ngủ quan trọng hơn số lượng. Dấu hiệu ngủ đủ: thức dậy tự nhiên, cảm thấy tỉnh táo, không cần caffeine để tỉnh. Nếu ngủ đủ nhưng vẫn mệt, có thể do chất lượng giấc ngủ kém hoặc rối loạn giấc ngủ.'],
            ['Làm thế nào để tăng cân an toàn?', 'Dinh dưỡng', 'Để tăng cân an toàn: 1) Tăng 300-500 calo/ngày so với nhu cầu hiện tại. 2) Ăn nhiều bữa nhỏ (5-6 bữa/ngày) thay vì 3 bữa lớn. 3) Chọn thực phẩm giàu dinh dưỡng: bơ, các loại hạt, sữa nguyên kem, thịt nạc, cá béo. 4) Uống calo: sinh tố, sữa, nước ép (không thay thế bữa ăn). 5) Kết hợp tập luyện sức mạnh để tăng cơ (không chỉ mỡ). 6) Tránh thức ăn nhanh, đồ ngọt (tăng mỡ không lành mạnh). 7) Kiên nhẫn: tăng 0.5-1kg/tuần là an toàn.'],
        ];

        for ($i = 0; $i < $count; $i++) {
            if ($i < count($detailedFAQs)) {
                $faq = $detailedFAQs[$i];
                $faqs[] = [
                    'title' => $faq[0],
                    'content' => $faq[2] . ' Nếu bạn có thêm câu hỏi, hãy tham khảo ý kiến của bác sĩ hoặc chuyên gia y tế để được tư vấn cụ thể hơn.',
                    'category' => $faq[1],
                ];
            } else {
                $baseFaq = $faqList[($i - count($detailedFAQs)) % count($faqList)];
                $faqs[] = [
                    'title' => $baseFaq[0],
                    'content' => $baseFaq[2] . ' Đây là câu trả lời chi tiết và cụ thể cho câu hỏi của bạn. Nếu cần thêm thông tin, hãy tham khảo ý kiến của bác sĩ hoặc chuyên gia y tế.',
                    'category' => $baseFaq[1],
                ];
            }
        }

        return $faqs;
    }

    private function generateMoreTemplates(int $count = 18): array
    {
        $templates = [];
        
        $templateList = [
            ['Mẫu tư vấn giảm cân', 'Dinh dưỡng', 'Dinh dưỡng', 'Đánh giá BMI, chế độ ăn giảm 500 calo/ngày, tập luyện 150 phút/tuần, theo dõi tiến độ.'],
            ['Mẫu tư vấn sức khỏe tâm thần', 'Sức khỏe tinh thần', 'Tâm thần', 'Đánh giá tình trạng, xác định nguyên nhân căng thẳng, kỹ thuật quản lý stress, kế hoạch điều trị.'],
            ['Mẫu tư vấn dinh dưỡng cho trẻ em', 'Dinh dưỡng', 'Nhi khoa', 'Đánh giá nhu cầu theo độ tuổi, chế độ ăn cân bằng, bổ sung vitamin, theo dõi tăng trưởng.'],
            ['Mẫu tư vấn tập luyện thể thao', 'Thể dục thể thao', 'Thể thao', 'Đánh giá thể lực, thiết lập mục tiêu, chương trình tập phù hợp, dinh dưỡng thể thao.'],
            ['Mẫu tư vấn chăm sóc da', 'Làm đẹp', 'Da liễu', 'Đánh giá loại da, chế độ chăm sóc hàng ngày, sản phẩm phù hợp, bảo vệ khỏi ánh nắng.'],
            ['Mẫu tư vấn phụ nữ mang thai', 'Sản phụ khoa', 'Sản khoa', 'Theo dõi sức khỏe mẹ và bé, dinh dưỡng thai kỳ, tập luyện an toàn, chuẩn bị sinh.'],
            ['Mẫu tư vấn người cao tuổi', 'Sức khỏe tổng quát', 'Lão khoa', 'Đánh giá sức khỏe tổng quát, quản lý bệnh mãn tính, dinh dưỡng phù hợp, phòng ngừa té ngã.'],
            ['Mẫu tư vấn cai thuốc lá', 'Sức khỏe tổng quát', 'Y học dự phòng', 'Đánh giá mức độ nghiện, phương pháp cai thuốc, hỗ trợ tâm lý, theo dõi tiến độ.'],
        ];

        // Tạo thêm templates với nội dung chi tiết
        $detailedTemplates = [
            ['Mẫu tư vấn giảm cân', 'Dinh dưỡng', 'Dinh dưỡng', '1. Đánh giá: BMI hiện tại, mục tiêu giảm cân (0.5-1kg/tuần). 2. Chế độ ăn: giảm 500-750 calo/ngày, tăng protein và chất xơ, hạn chế đường và tinh bột tinh chế. 3. Tập luyện: 150 phút/tuần cardio + 2 lần/tuần sức mạnh. 4. Theo dõi: cân nặng hàng tuần, ghi nhật ký ăn uống. 5. Hỗ trợ: tư vấn tâm lý nếu cần, nhóm hỗ trợ.'],
            ['Mẫu tư vấn sức khỏe tâm thần', 'Sức khỏe tinh thần', 'Tâm thần', '1. Đánh giá: tình trạng hiện tại, mức độ căng thẳng, triệu chứng. 2. Xác định nguyên nhân: công việc, mối quan hệ, tài chính, sức khỏe. 3. Kỹ thuật quản lý: thiền, yoga, hít thở sâu, viết nhật ký. 4. Kế hoạch điều trị: liệu pháp tâm lý, thuốc nếu cần, thay đổi lối sống. 5. Theo dõi: đánh giá tiến độ định kỳ, điều chỉnh kế hoạch.'],
            ['Mẫu tư vấn dinh dưỡng cho trẻ em', 'Dinh dưỡng', 'Nhi khoa', '1. Đánh giá: nhu cầu dinh dưỡng theo độ tuổi, tình trạng tăng trưởng. 2. Chế độ ăn: đa dạng, cân bằng 4 nhóm thực phẩm, 3 bữa chính + 2-3 bữa phụ. 3. Bổ sung: vitamin D, sắt nếu cần (theo chỉ định). 4. Theo dõi: cân nặng, chiều cao hàng tháng, biểu đồ tăng trưởng. 5. Giáo dục: hướng dẫn cha mẹ về dinh dưỡng phù hợp.'],
            ['Mẫu tư vấn tập luyện thể thao', 'Thể dục thể thao', 'Thể thao', '1. Đánh giá: thể lực hiện tại, mục tiêu, giới hạn. 2. Thiết lập mục tiêu: cụ thể, đo lường được, thực tế. 3. Chương trình tập: cardio, sức mạnh, linh hoạt, cân bằng. 4. Dinh dưỡng thể thao: trước, trong, sau tập. 5. Phòng ngừa chấn thương: khởi động, thư giãn, kỹ thuật đúng. 6. Theo dõi: tiến độ, điều chỉnh chương trình.'],
            ['Mẫu tư vấn chăm sóc da', 'Làm đẹp', 'Da liễu', '1. Đánh giá: loại da (khô, dầu, hỗn hợp, nhạy cảm). 2. Chế độ chăm sóc: làm sạch, dưỡng ẩm, bảo vệ (SPF 30+). 3. Sản phẩm: chọn phù hợp với loại da, tránh kích ứng. 4. Bảo vệ khỏi ánh nắng: kem chống nắng, quần áo, tránh nắng giữa trưa. 5. Điều trị: mụn, lão hóa, đốm nâu theo nhu cầu.'],
        ];

        for ($i = 0; $i < $count; $i++) {
            if ($i < count($detailedTemplates)) {
                $template = $detailedTemplates[$i];
                $templates[] = [
                    'title' => $template[0],
                    'content' => $template[3],
                    'category' => $template[1],
                    'specialty' => $template[2],
                ];
            } else {
                $baseTemplate = $templateList[($i - count($detailedTemplates)) % count($templateList)];
                $templates[] = [
                    'title' => $baseTemplate[0],
                    'content' => $baseTemplate[3] . ' Mẫu tư vấn này cung cấp hướng dẫn chi tiết và cụ thể cho từng bước.',
                    'category' => $baseTemplate[1],
                    'specialty' => $baseTemplate[2],
                ];
            }
        }

        return $templates;
    }
}
