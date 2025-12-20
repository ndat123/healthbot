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
        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('health_profile_id')->nullable()->constrained('health_profiles')->onDelete('set null');
            $table->string('title');
            $table->text('plan_data')->comment('JSON: daily meal plans, nutritional info, etc.');
            $table->integer('duration_days');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'paused'])->default('active');
            $table->text('dietary_preferences')->nullable();
            $table->text('allergies_restrictions')->nullable();
            $table->decimal('daily_calories', 6, 2)->nullable();
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
        Schema::dropIfExists('nutrition_plans');
    }
};

