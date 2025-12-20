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
        Schema::create('medical_content', function (Blueprint $table) {
            $table->id();
            $table->enum('content_type', ['knowledge_base', 'faq', 'template'])->default('knowledge_base');
            $table->string('title');
            $table->text('content');
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->string('specialty')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('views_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_content');
    }
};

