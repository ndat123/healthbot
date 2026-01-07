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
        $doctors = Doctor::all();
        
        // Sample reviews for each doctor
        $sampleReviews = [
            [
                ['rating' => 5, 'comment' => 'Dr. Johnson is excellent! She took the time to explain everything clearly and made me feel comfortable throughout the consultation.'],
                ['rating' => 5, 'comment' => 'Very professional and knowledgeable. Highly recommend!'],
                ['rating' => 4, 'comment' => 'Great doctor, but the wait time was a bit long. Overall good experience.'],
            ],
            [
                ['rating' => 5, 'comment' => 'Dr. Chen is wonderful! He listens carefully and provides excellent care.'],
                ['rating' => 5, 'comment' => 'Best general practitioner I\'ve ever had. Very thorough and caring.'],
                ['rating' => 4, 'comment' => 'Good doctor, very patient and understanding.'],
            ],
            [
                ['rating' => 5, 'comment' => 'Dr. Rodriguez helped me with my skin condition. Excellent results!'],
                ['rating' => 4, 'comment' => 'Professional and friendly. Would recommend to others.'],
                ['rating' => 5, 'comment' => 'Great dermatologist, very knowledgeable about skin care.'],
            ],
            [
                ['rating' => 5, 'comment' => 'Dr. Wilson is amazing with children. My kids love him!'],
                ['rating' => 5, 'comment' => 'Best pediatrician ever! Very caring and patient.'],
                ['rating' => 4, 'comment' => 'Good doctor, always available when needed.'],
            ],
            [
                ['rating' => 5, 'comment' => 'Dr. Anderson helped diagnose my condition. Very thorough examination.'],
                ['rating' => 4, 'comment' => 'Professional neurologist, good at explaining complex medical terms.'],
                ['rating' => 5, 'comment' => 'Excellent care and follow-up. Highly recommend!'],
            ],
            [
                ['rating' => 5, 'comment' => 'Dr. Taylor performed my knee surgery. Excellent results and recovery!'],
                ['rating' => 5, 'comment' => 'Great orthopedic surgeon. Very skilled and professional.'],
                ['rating' => 4, 'comment' => 'Good doctor, helped me recover from my injury.'],
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



