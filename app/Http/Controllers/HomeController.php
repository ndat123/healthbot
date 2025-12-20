<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy feedback tích cực từ database (general_feedback hoặc feature_request)
        // Chỉ lấy những feedback đã được reviewed hoặc resolved
        $testimonials = Feedback::with('user')
            ->whereIn('type', ['general_feedback', 'feature_request'])
            ->whereIn('status', ['reviewed', 'resolved'])
            ->whereNotNull('message')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($feedback) {
                // Tính rating dựa trên type (general_feedback = 5 sao, feature_request = 4-5 sao)
                $rating = $feedback->type === 'general_feedback' ? 5 : 4;
                
                // Lấy tên và role từ user, nếu không có thì dùng default
                $userName = $feedback->user->name ?? 'Anonymous User';
                $userRole = $feedback->user->role 
                    ? ucfirst($feedback->user->role) 
                    : ($feedback->type === 'general_feedback' ? 'Client' : 'User');
                
                return [
                    'id' => $feedback->id,
                    'name' => $userName,
                    'role' => $userRole,
                    'message' => $feedback->message,
                    'rating' => $rating,
                    'created_at' => $feedback->created_at,
                ];
            })
            ->toArray();

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