<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\UserSetting;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

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
     * Show settings page
     */
    public function settings()
    {
        $user = Auth::user();
        
        // Use existing settings retrieval logic or refactor
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
        
        // Get reminders
        $reminders = \App\Models\Reminder::where('user_id', $user->id)
            ->orderBy('reminder_time', 'asc')
            ->get();
        
        return view('profile.settings', compact('user', 'settings', 'reminders'));
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

        // Handle avatar upload to R2
        if ($request->hasFile('avatar')) {
            try {
                // Check R2 configuration first
                $r2Key = env('R2_ACCESS_KEY_ID');
                $r2Secret = env('R2_SECRET_ACCESS_KEY');
                $r2Configured = !empty($r2Key) && !empty($r2Secret) 
                    && $r2Key !== 'your_r2_access_key_id' 
                    && $r2Secret !== 'your_r2_secret_access_key';
                
                // Delete old avatar if exists (check both public and r2)
                if ($user->avatar) {
                    // Try to delete from R2 first
                    if ($r2Configured) {
                        try {
                            if (Storage::disk('r2')->exists($user->avatar)) {
                                Storage::disk('r2')->delete($user->avatar);
                            }
                        } catch (\Exception $e) {
                            \Log::warning('R2 delete failed: ' . $e->getMessage());
                        }
                    }
                    // Also try to delete from public disk (for backward compatibility)
                    try {
                        if (Storage::disk('public')->exists($user->avatar)) {
                            Storage::disk('public')->delete($user->avatar);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Public delete failed: ' . $e->getMessage());
                    }
                }
                
                // Try to upload to R2 first, fallback to public if R2 not available
                $avatarPath = null;
                $uploadedToR2 = false;
                
                if ($r2Configured) {
                    try {
                        // Upload to R2
                        \Log::info('Attempting R2 upload', [
                            'bucket' => env('R2_BUCKET', 'healthbot'),
                            'endpoint' => env('R2_ENDPOINT'),
                            'user_id' => $user->id
                        ]);
                        
                        // Upload to R2 root (no folder) to match URL format: https://pub-xxx.r2.dev/filename.png
                        $fileName = $user->id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
                        
                        // Use S3 client directly (Laravel Storage facade doesn't work reliably with R2)
                        try {
                            $s3Client = new S3Client([
                                'version' => 'latest',
                                'region' => env('R2_REGION', 'auto'),
                                'endpoint' => env('R2_ENDPOINT'),
                                'credentials' => [
                                    'key' => env('R2_ACCESS_KEY_ID'),
                                    'secret' => env('R2_SECRET_ACCESS_KEY'),
                                ],
                                'use_path_style_endpoint' => true,
                                'http' => [
                                    'verify' => false,
                                ],
                            ]);
                            
                            // Get file content
                            $fileContent = file_get_contents($request->file('avatar')->getRealPath());
                            $contentType = $request->file('avatar')->getMimeType();
                            
                            // Upload to R2
                            $result = $s3Client->putObject([
                                'Bucket' => env('R2_BUCKET', 'healthbot'),
                                'Key' => $fileName,
                                'Body' => $fileContent,
                                'ContentType' => $contentType,
                                'ACL' => 'public-read',
                            ]);
                            
                            // Use the fileName as the path (stored in database)
                            $avatarPath = $fileName;
                            
                            // Verify by reading back
                            try {
                                $verifyResult = $s3Client->getObject([
                                    'Bucket' => env('R2_BUCKET', 'healthbot'),
                                    'Key' => $fileName,
                                ]);
                                $verifyContent = $verifyResult['Body']->getContents();
                                
                                \Log::info('R2 file verified by reading back', [
                                    'path' => $avatarPath,
                                    'size' => strlen($verifyContent),
                                    'user_id' => $user->id,
                                    'etag' => $result->get('ETag')
                                ]);
                            } catch (AwsException $verifyError) {
                                \Log::warning('R2 file verification failed, but upload may still be successful: ' . $verifyError->getMessage());
                            }
                            
                            $uploadedToR2 = true;
                            \Log::info('Avatar uploaded to R2 successfully', [
                                'path' => $avatarPath,
                                'user_id' => $user->id,
                                'file_size' => $request->file('avatar')->getSize(),
                                'file_name' => $request->file('avatar')->getClientOriginalName(),
                                'etag' => $result->get('ETag')
                            ]);
                            
                        } catch (AwsException $e) {
                            \Log::error('R2 upload failed (AWS Exception)', [
                                'error' => $e->getMessage(),
                                'code' => $e->getAwsErrorCode(),
                                'user_id' => $user->id,
                                'file_name' => $fileName
                            ]);
                            throw new \Exception('R2 upload failed: ' . $e->getAwsErrorMessage() . ' (Code: ' . $e->getAwsErrorCode() . ')');
                        } catch (\Exception $e) {
                            \Log::error('R2 upload failed', [
                                'error' => $e->getMessage(),
                                'user_id' => $user->id,
                                'file_name' => $fileName,
                                'trace' => $e->getTraceAsString()
                            ]);
                            throw new \Exception('R2 upload failed: ' . $e->getMessage());
                        }
                    } catch (\Exception $e) {
                        // R2 upload failed, fallback to public
                        \Log::warning('R2 upload failed, falling back to public storage', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id
                        ]);
                        $avatarPath = null; // Reset to try public storage
                    }
                }
                
                // Fallback to public storage if R2 not configured or failed
                if (!$uploadedToR2 || !$avatarPath) {
                    // For local storage, still use avatars folder
                    $avatarPath = $request->file('avatar')->store('avatars', 'public');
                    
                    if ($avatarPath === false || empty($avatarPath)) {
                        throw new \Exception('Public storage upload also failed');
                    }
                    
                    \Log::info('Avatar uploaded to public storage', [
                        'path' => $avatarPath,
                        'user_id' => $user->id,
                        'r2_configured' => $r2Configured
                    ]);
                }
                
                // Ensure avatar path is set and saved to database
                if (empty($avatarPath)) {
                    throw new \Exception('Avatar upload failed - no path returned from storage');
                }
                
                $validated['avatar'] = $avatarPath;
                
            } catch (\Exception $e) {
                \Log::error('Avatar upload error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'user_id' => $user->id
                ]);
                return back()->withErrors(['avatar' => 'Không thể upload avatar: ' . $e->getMessage()]);
            }
        }

        $user->update($validated);

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
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
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Cập nhật mật khẩu thành công!');
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

        // Add user_id to validated data
        $validated['user_id'] = $user->id;

        try {
            $settings = UserSetting::updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );

            return redirect()->route('settings.index')
                ->with('success', 'Cập nhật cài đặt thành công!');
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Settings update error: ' . $e->getMessage());
            
            return redirect()->route('settings.index')
                ->with('error', 'Không thể cập nhật cài đặt. Vui lòng đảm bảo migration database đã được chạy: php artisan migrate');
        }
    }


    /**
     * Update selected nutrition plan
     */
    public function updateNutritionPlan(Request $request)
    {
        $request->validate([
            'nutrition_plan_id' => 'required|exists:nutrition_plans,id',
        ]);

        $user = Auth::user();
        
        // Ensure user owns the plan
        $plan = \App\Models\NutritionPlan::where('id', $request->nutrition_plan_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Update settings
        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            ['selected_nutrition_plan_id' => $plan->id]
        );

        return redirect()->back()->with('success', 'Đã lưu kế hoạch dinh dưỡng vào cài đặt!');
    }

    /**
     * Update selected health plan
     */
    public function updateHealthPlan(Request $request)
    {
        $request->validate([
            'health_plan_id' => 'required|exists:health_plans,id',
        ]);

        $user = Auth::user();
        
        // Ensure user owns the plan
        $plan = \App\Models\HealthPlan::where('id', $request->health_plan_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Update settings
        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            ['selected_health_plan_id' => $plan->id]
        );

        return redirect()->back()->with('success', 'Đã lưu kế hoạch sức khỏe vào cài đặt!');
    }
}

