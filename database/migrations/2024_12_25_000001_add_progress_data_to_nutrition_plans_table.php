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
        Schema::table('nutrition_plans', function (Blueprint $table) {
            $table->text('progress_data')->nullable()->after('plan_data')->comment('JSON: daily progress tracking');
            $table->integer('completion_percentage')->default(0)->after('progress_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutrition_plans', function (Blueprint $table) {
            $table->dropColumn(['progress_data', 'completion_percentage']);
        });
    }
};











