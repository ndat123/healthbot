<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('health_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('health_profile_id')->nullable()->constrained('health_profiles')->onDelete('set null');
            $table->string('title');
            $table->text('plan_data')->comment('JSON: daily plans, recommendations, etc.');
            $table->integer('duration_days');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'paused', 'cancelled'])->default('active');
            $table->text('progress_data')->nullable()->comment('JSON: daily progress tracking');
            $table->integer('completion_percentage')->default(0);
            $table->text('ai_prompt_used')->nullable();
            $table->text('ai_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_plans');
    }
};

