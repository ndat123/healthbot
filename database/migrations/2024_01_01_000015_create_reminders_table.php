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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('reminder_type', ['medication', 'water', 'exercise', 'meal', 'appointment', 'other'])->default('other');
            $table->string('title');
            $table->text('description')->nullable();
            $table->time('reminder_time');
            $table->json('reminder_days')->nullable()->comment('Days of week: [1,2,3,4,5] for Mon-Fri');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_recurring')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
