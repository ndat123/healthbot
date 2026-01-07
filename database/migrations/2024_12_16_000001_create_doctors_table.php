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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialization');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('education')->nullable();
            $table->json('certifications')->nullable();
            $table->json('specialties')->nullable();
            $table->json('hospital_affiliations')->nullable();
            $table->json('languages')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->string('available_hours')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};



