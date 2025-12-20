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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'premium', 'doctor', 'admin'])->default('user')->after('email');
            $table->enum('status', ['active', 'inactive', 'locked'])->default('active')->after('role');
            $table->timestamp('last_login')->nullable()->after('status');
            $table->string('phone')->nullable()->after('last_login');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('avatar')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'status',
                'last_login',
                'phone',
                'date_of_birth',
                'gender',
                'address',
                'avatar'
            ]);
        });
    }
};

