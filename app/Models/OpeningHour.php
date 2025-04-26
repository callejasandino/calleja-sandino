<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpeningHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_active',
        'is_biweekly',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_biweekly' => 'boolean',
    ];

    public function breakTimes(): HasMany
    {
        return $this->hasMany(BreakTime::class);
    }

    public static function isOpenNow(): bool
    {
        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeek;
        $currentTime = $now->format('H:i:s');
        
        // Check if today is open
        $todayHours = self::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();
            
        if (!$todayHours) {
            return false;
        }
        
        // If it's a biweekly schedule (for Saturday)
        if ($todayHours->is_biweekly && $dayOfWeek == 6) {
            // Check if this is an "open" Saturday (every other week)
            $weekNumber = $now->weekOfYear;
            if ($weekNumber % 2 !== 0) {
                return false;
            }
        }
        
        // Check if the current time is within opening hours
        if ($currentTime < $todayHours->open_time || $currentTime > $todayHours->close_time) {
            return false;
        }
        
        // Check if it's during a break time
        foreach ($todayHours->breakTimes as $breakTime) {
            if ($currentTime >= $breakTime->start_time && $currentTime <= $breakTime->end_time) {
                return false;
            }
        }
        
        return true;
    }

    public static function getNextOpenDate(): ?Carbon
    {
        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeek;
        $currentTime = $now->format('H:i:s');
        
        // Check if we're currently in a break time
        $todayHours = self::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();
            
        if ($todayHours) {
            // Check if any break times are happening now
            foreach ($todayHours->breakTimes as $breakTime) {
                if ($currentTime >= $breakTime->start_time && $currentTime <= $breakTime->end_time) {
                    return Carbon::createFromFormat('H:i:s', $breakTime->end_time);
                }
            }
            
            // If we're before opening time today
            if ($currentTime < $todayHours->open_time) {
                return Carbon::createFromFormat('H:i:s', $todayHours->open_time);
            }
            
            // If we're after closing time or not open today, find the next day
        }
        
        // Find the next open day
        for ($i = 1; $i <= 7; $i++) {
            $nextDay = ($dayOfWeek + $i) % 7;
            $nextOpenDay = self::where('day_of_week', $nextDay)
                ->where('is_active', true)
                ->first();
                
            if ($nextOpenDay) {
                if ($nextOpenDay->is_biweekly && $nextDay == 6) {
                    // For biweekly Saturday, check if the next Saturday is an "open" one
                    $nextWeekSaturday = $now->copy()->addDays($i);
                    $weekNumber = $nextWeekSaturday->weekOfYear;
                    if ($weekNumber % 2 !== 0) {
                        continue; // Skip this Saturday as it's not an "open" one
                    }
                }
                
                $nextDate = $now->copy()->addDays($i)->setTimeFromTimeString($nextOpenDay->open_time);
                return $nextDate;
            }
        }
        
        return null;
    }
} 