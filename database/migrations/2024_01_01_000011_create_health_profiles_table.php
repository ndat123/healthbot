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
        Schema::create('health_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
            $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kg');
            $table->decimal('bmi', 4, 2)->nullable();
            $table->text('medical_history')->nullable()->comment('JSON or text');
            $table->text('allergies')->nullable();
            $table->text('lifestyle_habits')->nullable()->comment('JSON: exercise_frequency, sleep_hours, smoking, alcohol, etc.');
            $table->text('health_goals')->nullable()->comment('JSON: weight_loss, muscle_gain, disease_control, etc.');
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable();
            $table->decimal('blood_sugar', 5, 2)->nullable()->comment('Blood sugar level');
            $table->text('other_metrics')->nullable()->comment('JSON for other health metrics');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_profiles');
    }
};

