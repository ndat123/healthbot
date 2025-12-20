<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('scenario_data')->nullable()->comment('JSON: training data, examples, etc.');
            $table->enum('status', ['pending', 'training', 'trained', 'failed'])->default('pending');
            $table->integer('training_progress')->default(0)->comment('0-100');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_scenarios');
    }
};




