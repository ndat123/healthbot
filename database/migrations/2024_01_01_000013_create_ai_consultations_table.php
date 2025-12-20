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
        Schema::create('ai_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('health_profile_id')->nullable()->constrained('health_profiles')->onDelete('set null');
            $table->string('session_id')->unique();
            $table->string('topic')->nullable();
            $table->enum('consultation_type', ['symptoms', 'nutrition', 'lifestyle', 'general', 'specialist_referral'])->default('general');
            $table->text('user_message');
            $table->text('ai_response');
            $table->enum('emergency_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->json('context_data')->nullable()->comment('User profile context, previous messages, etc.');
            $table->json('suggested_specialists')->nullable();
            $table->boolean('disclaimer_acknowledged')->default(false);
            $table->integer('message_count')->default(1);
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_consultations');
    }
};

