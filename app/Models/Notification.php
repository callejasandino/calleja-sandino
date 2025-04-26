<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'next_notification_date',
        'is_active',
    ];

    protected $casts = [
        'next_notification_date' => 'datetime',
        'is_active' => 'boolean',
    ];
} 