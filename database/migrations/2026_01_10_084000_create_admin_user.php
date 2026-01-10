<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if user exists
        if (!User::where('email', 'admin@business.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@business.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $user = User::where('email', 'admin@business.com')->first();
        if ($user) {
            $user->delete();
        }
    }
};
