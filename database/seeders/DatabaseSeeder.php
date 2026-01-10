<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed feedback mẫu
        $this->call(FeedbackSeeder::class);
        
        // Seed doctors
        $this->call(DoctorSeeder::class);
        
        // Seed doctor reviews
        $this->call(DoctorReviewSeeder::class);
        
        // Seed medical content
        $this->call(MedicalContentSeeder::class);
        
        // Seed training scenarios
        $this->call(TrainingScenarioSeeder::class);
        
        // Seed AI sessions (optional - chỉ chạy nếu muốn có dữ liệu mẫu)
        // $this->call(AISessionSeeder::class);
    }
}
