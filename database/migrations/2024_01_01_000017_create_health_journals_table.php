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
        Schema::create('health_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('journal_date');
            $table->text('symptoms')->nullable()->comment('Daily symptoms notes');
            $table->text('food_diary')->nullable()->comment('Food intake journal');
            $table->text('exercise_log')->nullable()->comment('Exercise/activity log');
            $table->enum('mood', ['excellent', 'good', 'okay', 'poor', 'very_poor'])->nullable();
            $table->integer('mood_score')->nullable()->comment('1-10 scale');
            $table->text('mood_notes')->nullable();
            $table->json('ai_suggestions')->nullable()->comment('AI-generated health suggestions');
            $table->json('ai_warnings')->nullable()->comment('AI-generated health warnings');
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->boolean('doctor_recommended')->default(false);
            $table->text('doctor_recommendation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'journal_date']);
            $table->index(['user_id', 'journal_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_journals');
    }
};

