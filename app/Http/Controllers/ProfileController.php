<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\UserSetting;

class ProfileController extends Controller
{
    /**
     * Show user profile
     */
    public function index()
    {
        $user = Auth::user();
        
        // Kiểm tra xem bảng có tồn tại không
        try {
            $settings = UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'email_notifications' => true,
                    'sms_notifications' => false,
                    'health_reminders' => true,
                    'appointment_reminders' => true,
                    'newsletter_subscription' => false,
                    'language' => 'en',
                    'timezone' => 'UTC',
                    'privacy_level' => 'private',
                    'share_health_data' => false,
                    'allow_ai_learning' => true,
                ]
            );
        } catch (\Exception $e) {
            // Nếu bảng chưa tồn tại, tạo object settings mặc định
            $settings = (object) [
                'email_notifications' => true,
                'sms_notifications' => false,
                'health_reminders' => true,
                'appointment_reminders' => true,
                'newsletter_subscription' => false,
                'language' => 'en',
                'timezone' => 'UTC',
                'privacy_level' => 'private',
                'share_health_data' => false,
                'allow_ai_learning' => true,
            ];
        }
        
        return view('profile.index', compact('user', 'settings'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Update user settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        // Validate non-checkbox fields
        $validated = $request->validate([
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'privacy_level' => 'nullable|in:public,friends,private',
        ]);

        // Handle checkbox fields (checkboxes don't send value if unchecked)
        $booleanFields = [
            'email_notifications',
            'sms_notifications',
            'health_reminders',
            'appointment_reminders',
            'newsletter_subscription',
            'share_health_data',
            'allow_ai_learning',
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) && $request->input($field) == '1';
        }

        try {
            $settings = UserSetting::updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );

            return redirect()->route('profile.index', ['#settings'])
                ->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Settings update error: ' . $e->getMessage());
            
            return redirect()->route('profile.index', ['#settings'])
                ->with('error', 'Failed to update settings. Please make sure the database migration has been run: php artisan migrate');
        }
    }
}

