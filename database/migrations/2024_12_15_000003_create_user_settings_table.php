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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('health_reminders')->default(true);
            $table->boolean('appointment_reminders')->default(true);
            $table->boolean('newsletter_subscription')->default(false);
            $table->string('language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->enum('privacy_level', ['public', 'friends', 'private'])->default('private');
            $table->boolean('share_health_data')->default(false);
            $table->boolean('allow_ai_learning')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};



