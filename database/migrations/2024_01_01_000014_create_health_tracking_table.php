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
        Schema::create('health_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tracking_date');
            $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kg');
            $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
            $table->decimal('bmi', 4, 2)->nullable();
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable();
            $table->decimal('blood_sugar', 5, 2)->nullable()->comment('Blood sugar in mg/dL');
            $table->integer('heart_rate')->nullable()->comment('Heart rate in bpm');
            $table->decimal('body_temperature', 4, 2)->nullable()->comment('Temperature in Celsius');
            $table->text('notes')->nullable();
            $table->json('other_metrics')->nullable()->comment('Additional health metrics');
            $table->timestamps();
            
            $table->index(['user_id', 'tracking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_tracking');
    }
};

