<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        // Testimonials tiếng Việt
        $testimonials = [
            [
                'id' => 1,
                'name' => 'Nguyễn Thị Lan',
                'role' => 'Người dùng',
                'message' => 'AI HealthBot đã thay đổi hoàn toàn cách tiếp cận sức khỏe của tôi. Kế hoạch cá nhân hóa mà họ tạo ra đã tạo nên sự khác biệt đáng kể trong sức khỏe tổng thể của tôi.',
                'rating' => 5,
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Trần Văn Minh',
                'role' => 'Người dùng',
                'message' => 'Tính năng chẩn đoán AI đã giúp tôi tiết kiệm rất nhiều thời gian chờ đợi các chuyên gia y tế. Độ chính xác của phân tích rất ấn tượng và các khuyến nghị rất phù hợp.',
                'rating' => 5,
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Lê Thị Hương',
                'role' => 'Khách hàng',
                'message' => 'Tư vấn dinh dưỡng mà tôi nhận được đã thay đổi cuộc sống của tôi. Kế hoạch bữa ăn được điều chỉnh hoàn hảo theo nhu cầu và sở thích của tôi, và tôi đã thấy kết quả tuyệt vời chỉ sau vài tuần.',
                'rating' => 5,
                'created_at' => now(),
            ],
        ];

        return view('welcome', compact('testimonials'));
    }

    public function contact(Request $request)
    {
        // Validate dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Tìm hoặc tạo user từ email (nếu chưa có account)
        $user = User::where('email', $validated['email'])->first();
        
        // Nếu không có user, tạo user mới với thông tin từ form
        if (!$user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt(uniqid()), // Random password vì user chưa đăng ký
                'role' => 'user',
                'status' => 'active',
            ]);
        }

        // Tạo feedback từ form contact
        Feedback::create([
            'user_id' => $user->id,
            'type' => 'general_feedback', // Contact form được coi là general feedback
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'pending', // Chờ admin review
        ]);

        return redirect()->route('home', ['#contact'])
            ->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}