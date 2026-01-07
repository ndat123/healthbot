<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'type',
        'status',
        'file_path',
        'data',
        'generated_at',
    ];

    protected $casts = [
        'data' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Scope to get ready reports.
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope to get reports by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Mark report as ready.
     */
    public function markAsReady(string $filePath): bool
    {
        $this->update([
            'status' => 'ready',
            'file_path' => $filePath,
            'generated_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark report as failed.
     */
    public function markAsFailed(): bool
    {
        $this->update([
            'status' => 'failed',
        ]);

        return true;
    }
}

