<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalContent extends Model
{
    use HasFactory;

    protected $table = 'medical_content';

    protected $fillable = [
        'content_type',
        'title',
        'content',
        'category',
        'tags',
        'specialty',
        'status',
        'created_by',
        'views_count',
        'helpful_count',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    /**
     * Get the user who created this content.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all users who bookmarked this content.
     */
    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'medical_content_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Check if a user has bookmarked this content.
     */
    public function isBookmarkedBy($userId)
    {
        return $this->bookmarkedBy()->where('user_id', $userId)->exists();
    }
}




