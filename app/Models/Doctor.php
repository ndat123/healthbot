<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'email',
        'phone',
        'bio',
        'education',
        'certifications',
        'specialties',
        'hospital_affiliations',
        'languages',
        'years_of_experience',
        'consultation_fee',
        'available_hours',
        'avatar',
        'status',
    ];

    protected $casts = [
        'certifications' => 'array',
        'specialties' => 'array',
        'hospital_affiliations' => 'array',
        'languages' => 'array',
        'consultation_fee' => 'decimal:2',
    ];

    /**
     * Get the reviews for the doctor.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class);
    }

    /**
     * Get the consultations for the doctor.
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get the messages for the doctor.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(DoctorMessage::class);
    }

    /**
     * Get the average rating for the doctor.
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get the total reviews count.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Check if doctor is available today
     */
    public function getAvailableTodayAttribute(): bool
    {
        // Simple logic - can be enhanced with actual availability checking
        return $this->status === 'active';
    }

    /**
     * Get next available time
     */
    public function getNextAvailableAttribute(): string
    {
        // Simple logic - can be enhanced with actual availability checking
        return $this->status === 'active' ? 'Today, 2:00 PM' : 'Tomorrow, 9:00 AM';
    }
}

