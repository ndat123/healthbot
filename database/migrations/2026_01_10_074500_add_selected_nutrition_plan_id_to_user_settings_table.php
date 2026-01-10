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
        Schema::table('user_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('selected_nutrition_plan_id')->nullable()->after('allow_ai_learning');
            // Assuming nutrition_plans table exists and has id. 
            // We can add foreign key constraint if nutrition_plans table exists.
            // Based on file list, 2024_01_01_000016_create_nutrition_plans_table.php exists.
            $table->foreign('selected_nutrition_plan_id')->references('id')->on('nutrition_plans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropForeign(['selected_nutrition_plan_id']);
            $table->dropColumn('selected_nutrition_plan_id');
        });
    }
};
