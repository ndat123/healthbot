<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'specialist_id',
        'consultation_date',
        'consultation_time',
        'status',
        'type',
        'symptoms',
        'diagnosis',
        'prescription',
        'notes',
        'fee',
        'reminder_sent_at',
    ];

    protected $casts = [
        'consultation_date' => 'date',
        'fee' => 'decimal:2',
        'reminder_sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns the consultation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the doctor for the consultation.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the specialist for the consultation (if exists).
     */
    public function specialist()
    {
        // Note: Specialist model may not exist
        // Uncomment if Specialist model exists
        // return $this->belongsTo(Specialist::class);
        return null;
    }
}

