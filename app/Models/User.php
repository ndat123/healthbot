<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Auth\Notifications\ResetPassword;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // Debug logging
        \Log::info('sendPasswordResetNotification called for user: ' . $this->email);

        // Generate the URL correctly. 
        // Note: route('password.reset', ...) generates a full URL by default.
        $url = route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ]);

        \Log::info('Generated Password Reset URL: ' . $url);

        try {
            // Fallback: Use raw curl via exec to bypass PHP HttpClient limitations with Cloudflare
            // This mimics the exact behavior of a command line curl request which is often less filtered
            $to = escapeshellarg($this->getEmailForPasswordReset());
            $subject = escapeshellarg('Đặt lại mật khẩu AI HealthBot');
            $content = view('emails.password_reset', ['url' => $url])->render();
            // Simplify content for curl to avoid shell escaping issues with complex HTML
            // We'll base64 encode the body to safely pass it through shell
            $content_encoded = base64_encode($content);
            
            // Construct the curl command
            $cmd = "curl -X POST \"https://api.love4awalk.xyz/send-email\" ";
            $cmd .= "-H \"Content-Type: application/json\" ";
            $cmd .= "-H \"Accept: */*\" ";
            $cmd .= "-H \"User-Agent: PostmanRuntime/7.36.0\" ";
            $cmd .= "-H \"Connection: keep-alive\" ";
            // Disable SSL verify because local environment often lacks proper CA certs
            $cmd .= "-k "; 
            
            // Build JSON payload manually
            $json_payload = json_encode([
                'to' => $this->getEmailForPasswordReset(),
                'subject' => 'Đặt lại mật khẩu AI HealthBot',
                'html_content' => $content
            ]);
            
            // Write payload to a temp file
            $tempFile = tempnam(sys_get_temp_dir(), 'mail_');
            file_put_contents($tempFile, $json_payload);
            
            $cmd .= "--data @" . $tempFile;
            
            \Log::info('Executing Curl Command: ' . $cmd);
            exec($cmd, $output, $return_var);
            
            // Cleanup
            unlink($tempFile);
            
            \Log::info('Curl Output: ' . implode("\n", $output));
            \Log::info('Curl Return Var: ' . $return_var);
            
            if ($return_var !== 0) {
                 \Log::error('Curl failed with code: ' . $return_var);
            }

            /*
            // Log response
            \Log::info('API Response Status: ' . $response->status());
            \Log::info('API Response Body: ' . $response->body());

            if (!$response->successful()) {
                \Log::error('API Request failed with status: ' . $response->status());
            }
            */

        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email via API: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'avatar',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's settings.
     */
    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications count with caching.
     */
    public function unreadNotificationsCount(): int
    {
        return \Cache::remember(
            "user.{$this->id}.unread_notifications_count",
            60, // Cache for 1 minute
            fn() => $this->notifications()->unread()->count()
        );
    }

    /**
     * Get the user's bookmarks.
     */
    public function bookmarks()
    {
        return $this->belongsToMany(MedicalContent::class, 'bookmarks', 'user_id', 'medical_content_id')
            ->withTimestamps();
    }

    /**
     * Get the avatar URL (from R2 or local storage)
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        $r2PublicUrl = config('filesystems.disks.r2.url', 'https://pub-07cce266cb7a4eff97bc6503d84b6470.r2.dev');
        
        // Check if avatar is stored in R2
        // R2 files have format: userid_timestamp.ext (e.g., 1_1234567890.png) - stored in root
        // OR: avatars/filename.jpg - stored in avatars folder (legacy)
        // Support common image formats including jfif
        if (preg_match('/^\d+_\d+\.(jpg|jpeg|png|gif|webp|jfif)$/i', $this->avatar)) {
            // New format: Direct R2 URL format: https://pub-xxx.r2.dev/filename.png
            return rtrim($r2PublicUrl, '/') . '/' . $this->avatar;
        }
        
        // Legacy: Check if avatar path starts with 'avatars/' - could be R2 or local
        if (str_starts_with($this->avatar, 'avatars/')) {
            // Assume R2 first (faster, no disk check)
            return rtrim($r2PublicUrl, '/') . '/' . $this->avatar;
        }

        // Fallback to local storage for backward compatibility
        return asset('storage/' . $this->avatar);
    }
}
