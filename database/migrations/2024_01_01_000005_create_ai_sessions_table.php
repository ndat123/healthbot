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
        Schema::create('ai_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('session_token')->unique();
            $table->text('user_query');
            $table->text('ai_response');
            $table->string('topic')->nullable();
            $table->enum('emergency_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->integer('duration_seconds')->nullable();
            $table->decimal('accuracy_score', 5, 2)->nullable();
            $table->integer('user_satisfaction')->nullable()->comment('Rating from 1-5');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_sessions');
    }
};

