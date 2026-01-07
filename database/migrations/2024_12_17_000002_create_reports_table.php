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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // User Analytics, AI Analytics, Health Analytics
            $table->enum('type', ['pdf', 'excel', 'csv'])->default('pdf');
            $table->enum('status', ['pending', 'ready', 'failed'])->default('pending');
            $table->string('file_path')->nullable();
            $table->json('data')->nullable(); // Store report data
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            
            $table->index(['category', 'status']);
            $table->index('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

