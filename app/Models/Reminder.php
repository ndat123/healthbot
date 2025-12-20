<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reminder_type',
        'title',
        'description',
        'reminder_time',
        'reminder_days',
        'is_active',
        'is_recurring',
        'start_date',
        'end_date',
        'last_triggered_at',
    ];

    protected $casts = [
        'reminder_days' => 'array',
        'is_active' => 'boolean',
        'is_recurring' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_triggered_at' => 'datetime',
    ];

    /**
     * Get the user that owns the reminder.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if reminder should trigger today.
     */
    public function shouldTriggerToday(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = Carbon::today();
        
        // Check date range
        if ($this->start_date && $today < $this->start_date) {
            return false;
        }
        
        if ($this->end_date && $today > $this->end_date) {
            return false;
        }

        // Check if today is in reminder_days
        if ($this->is_recurring && $this->reminder_days) {
            $dayOfWeek = $today->dayOfWeek; // 0 = Sunday, 6 = Saturday
            return in_array($dayOfWeek, $this->reminder_days);
        }

        return true;
    }
}
