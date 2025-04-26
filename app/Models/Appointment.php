<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'notes',
        'appointment_date',
        'appointment_time',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];
} 