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
            $table->unsignedBigInteger('selected_health_plan_id')->nullable()->after('selected_nutrition_plan_id');
            $table->foreign('selected_health_plan_id')->references('id')->on('health_plans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropForeign(['selected_health_plan_id']);
            $table->dropColumn('selected_health_plan_id');
        });
    }
};
