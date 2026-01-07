<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
