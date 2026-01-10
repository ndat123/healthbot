<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu reviews cũ (không dùng truncate vì có foreign key)
        DoctorReview::query()->delete();
        
        $doctors = Doctor::all();
        
        // Sample reviews for each doctor (tiếng Việt)
        $sampleReviews = [
            [
                ['rating' => 5, 'comment' => 'BS. Hương rất xuất sắc! Bác sĩ đã dành thời gian giải thích mọi thứ rõ ràng và khiến tôi cảm thấy thoải mái trong suốt buổi tư vấn.'],
                ['rating' => 5, 'comment' => 'Rất chuyên nghiệp và am hiểu. Tôi rất khuyên bạn nên đến khám!'],
                ['rating' => 4, 'comment' => 'Bác sĩ tốt, nhưng thời gian chờ đợi hơi lâu. Nhìn chung trải nghiệm tốt.'],
            ],
            [
                ['rating' => 5, 'comment' => 'BS. Minh rất tuyệt vời! Bác sĩ lắng nghe cẩn thận và cung cấp dịch vụ chăm sóc xuất sắc.'],
                ['rating' => 5, 'comment' => 'Bác sĩ đa khoa tốt nhất mà tôi từng gặp. Rất kỹ lưỡng và quan tâm.'],
                ['rating' => 4, 'comment' => 'Bác sĩ tốt, rất kiên nhẫn và thấu hiểu.'],
            ],
            [
                ['rating' => 5, 'comment' => 'BS. Mai đã giúp tôi điều trị tình trạng da. Kết quả xuất sắc!'],
                ['rating' => 4, 'comment' => 'Chuyên nghiệp và thân thiện. Sẽ giới thiệu cho người khác.'],
                ['rating' => 5, 'comment' => 'Bác sĩ da liễu tuyệt vời, rất am hiểu về chăm sóc da.'],
            ],
            [
                ['rating' => 5, 'comment' => 'BS. Đức rất tuyệt vời với trẻ em. Các con tôi rất yêu quý bác sĩ!'],
                ['rating' => 5, 'comment' => 'Bác sĩ nhi khoa tốt nhất! Rất quan tâm và kiên nhẫn.'],
                ['rating' => 4, 'comment' => 'Bác sĩ tốt, luôn sẵn sàng khi cần.'],
            ],
            [
                ['rating' => 5, 'comment' => 'BS. Lan đã giúp chẩn đoán tình trạng của tôi. Khám xét rất kỹ lưỡng.'],
                ['rating' => 4, 'comment' => 'Bác sĩ thần kinh chuyên nghiệp, giỏi giải thích các thuật ngữ y tế phức tạp.'],
                ['rating' => 5, 'comment' => 'Chăm sóc xuất sắc và theo dõi tốt. Rất khuyên bạn nên đến khám!'],
            ],
            [
                ['rating' => 5, 'comment' => 'BS. Hùng đã thực hiện phẫu thuật đầu gối cho tôi. Kết quả và phục hồi xuất sắc!'],
                ['rating' => 5, 'comment' => 'Bác sĩ phẫu thuật chỉnh hình tuyệt vời. Rất tài năng và chuyên nghiệp.'],
                ['rating' => 4, 'comment' => 'Bác sĩ tốt, đã giúp tôi phục hồi sau chấn thương.'],
            ],
        ];

        // Get or create a test user for reviews
        $testUser = User::firstOrCreate(
            ['email' => 'reviewer@example.com'],
            [
                'name' => 'Test Reviewer',
                'password' => bcrypt('password'),
            ]
        );

        foreach ($doctors as $index => $doctor) {
            if (isset($sampleReviews[$index])) {
                foreach ($sampleReviews[$index] as $reviewData) {
                    DoctorReview::create([
                        'doctor_id' => $doctor->id,
                        'user_id' => $testUser->id,
                        'rating' => $reviewData['rating'],
                        'comment' => $reviewData['comment'],
                    ]);
                }
            }
        }
    }
}



