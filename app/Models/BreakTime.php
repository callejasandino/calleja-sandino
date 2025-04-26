<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_hour_id',
        'start_time',
        'end_time',
    ];

    public function openingHour(): BelongsTo
    {
        return $this->belongsTo(OpeningHour::class);
    }
} 