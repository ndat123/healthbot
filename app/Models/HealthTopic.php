<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'search_count',
        'consultation_count',
        'description',
    ];
}




