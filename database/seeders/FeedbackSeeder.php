<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo hoặc lấy users mẫu
        $user1 = User::firstOrCreate(
            ['email' => 'sarah.johnson@example.com'],
            [
                'name' => 'Sarah Johnson',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'michael.chen@example.com'],
            [
                'name' => 'Michael Chen',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
            ]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'emily.rodriguez@example.com'],
            [
                'name' => 'Emily Rodriguez',
                'password' => Hash::make('password'),
                'role' => 'premium',
                'status' => 'active',
            ]
        );

        // Tạo 3 feedback mẫu
        $feedbacks = [
            [
                'user_id' => $user1->id,
                'type' => 'general_feedback',
                'subject' => 'Excellent Health Service',
                'message' => 'AI HealthBot completely transformed my approach to health. The personalized plan they created for me has made a significant difference in my overall well-being.',
                'status' => 'resolved',
                'reviewed_at' => now()->subDays(5),
            ],
            [
                'user_id' => $user2->id,
                'type' => 'feature_request',
                'subject' => 'Great AI Diagnostics',
                'message' => 'The AI diagnostics feature saved me countless hours of waiting for medical professionals. The accuracy of the analysis was impressive, and the recommendations were spot-on.',
                'status' => 'reviewed',
                'reviewed_at' => now()->subDays(3),
            ],
            [
                'user_id' => $user3->id,
                'type' => 'general_feedback',
                'subject' => 'Amazing Nutrition Consultation',
                'message' => 'The nutrition consultation I received was life-changing. The meal plan was tailored perfectly to my needs and preferences, and I\'ve seen amazing results in just a few weeks.',
                'status' => 'resolved',
                'reviewed_at' => now()->subDays(1),
            ],
        ];

        foreach ($feedbacks as $feedback) {
            Feedback::firstOrCreate(
                [
                    'user_id' => $feedback['user_id'],
                    'subject' => $feedback['subject'],
                ],
                $feedback
            );
        }
    }
}



