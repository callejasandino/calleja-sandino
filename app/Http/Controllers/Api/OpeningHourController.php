<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BreakTime;
use App\Models\OpeningHour;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpeningHourController extends Controller
{
    public function index(): JsonResponse
    {
        $openingHours = OpeningHour::with('breakTimes')->orderBy('day_of_week')->get();
        
        // Format the data for the frontend
        $weeklySchedule = [
            ['day' => 'Monday', 'isOpen' => false, 'openTime' => '', 'closeTime' => ''],
            ['day' => 'Tuesday', 'isOpen' => false, 'openTime' => '', 'closeTime' => ''],
            ['day' => 'Wednesday', 'isOpen' => false, 'openTime' => '', 'closeTime' => ''],
            ['day' => 'Thursday', 'isOpen' => false, 'openTime' => '', 'closeTime' => ''],
            ['day' => 'Friday', 'isOpen' => false, 'openTime' => '', 'closeTime' => ''],
            ['day' => 'Saturday', 'isOpen' => false, 'openTime' => '', 'closeTime' => ''],
            ['day' => 'Sunday', 'isOpen' => false, 'openTime' => '', 'closeTime' => '']
        ];
        
        $saturdaySchedule = [
            'enabled' => false,
            'openTime' => '09:00',
            'closeTime' => '13:00',
            'nextDate' => null
        ];
        
        foreach ($openingHours as $hour) {
            if ($hour->day_of_week < 7) {
                $dayIndex = $hour->day_of_week == 0 ? 6 : $hour->day_of_week - 1; // Convert 0 (Sunday) to 6
                $weeklySchedule[$dayIndex]['isOpen'] = $hour->is_active;
                if ($hour->is_active) {
                    $weeklySchedule[$dayIndex]['openTime'] = substr($hour->open_time, 0, 5);
                    $weeklySchedule[$dayIndex]['closeTime'] = substr($hour->close_time, 0, 5);
                }
                
                // If this is Saturday and biweekly, update the Saturday schedule
                if ($hour->day_of_week == 6 && $hour->is_biweekly) {
                    $saturdaySchedule['enabled'] = $hour->is_active;
                    $saturdaySchedule['openTime'] = substr($hour->open_time, 0, 5);
                    $saturdaySchedule['closeTime'] = substr($hour->close_time, 0, 5);
                }
            }
        }
        
        return response()->json([
            'weeklySchedule' => $weeklySchedule,
            'saturdaySchedule' => $saturdaySchedule
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i|after:open_time',
            'is_active' => 'boolean',
            'is_biweekly' => 'boolean',
            'break_times' => 'nullable|array',
            'break_times.*.start_time' => 'required_with:break_times|date_format:H:i',
            'break_times.*.end_time' => 'required_with:break_times|date_format:H:i|after:break_times.*.start_time',
        ]);

        // Check if we already have an entry for this day
        $existingHour = OpeningHour::where('day_of_week', $validated['day_of_week'])->first();
        
        if ($existingHour) {
            return response()->json([
                'message' => 'An opening hour for this day already exists. Please update the existing one.',
            ], 422);
        }

        $openingHour = OpeningHour::create([
            'day_of_week' => $validated['day_of_week'],
            'open_time' => $validated['open_time'],
            'close_time' => $validated['close_time'],
            'is_active' => $validated['is_active'] ?? true,
            'is_biweekly' => $validated['is_biweekly'] ?? false,
        ]);

        // Add break times if provided
        if (!empty($validated['break_times'])) {
            foreach ($validated['break_times'] as $breakTime) {
                $openingHour->breakTimes()->create([
                    'start_time' => $breakTime['start_time'],
                    'end_time' => $breakTime['end_time'],
                ]);
            }
        }

        return response()->json($openingHour->load('breakTimes'), 201);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'weeklySchedule' => 'required|array',
            'weeklySchedule.*.day' => 'required|string',
            'weeklySchedule.*.isOpen' => 'required|boolean',
            'weeklySchedule.*.openTime' => 'required_if:weeklySchedule.*.isOpen,true|nullable|string',
            'weeklySchedule.*.closeTime' => 'required_if:weeklySchedule.*.isOpen,true|nullable|string',
            'saturdaySchedule' => 'required|array',
            'saturdaySchedule.enabled' => 'required|boolean',
            'saturdaySchedule.openTime' => 'required_if:saturdaySchedule.enabled,true|nullable|string',
            'saturdaySchedule.closeTime' => 'required_if:saturdaySchedule.enabled,true|nullable|string',
        ]);

        // Start a database transaction
        DB::beginTransaction();
        
        try {
            $dayMap = [
                'Monday' => 1,
                'Tuesday' => 2,
                'Wednesday' => 3,
                'Thursday' => 4, 
                'Friday' => 5,
                'Saturday' => 6,
                'Sunday' => 0
            ];

            // Process weekly schedule
            foreach ($request->weeklySchedule as $day) {
                $dayOfWeek = $dayMap[$day['day']];
                
                // Skip Saturday if it's biweekly (handled separately)
                if ($day['day'] === 'Saturday' && $request->saturdaySchedule['enabled']) {
                    continue;
                }
                
                $openingHour = OpeningHour::updateOrCreate(
                    ['day_of_week' => $dayOfWeek],
                    [
                        'open_time' => $day['isOpen'] ? $day['openTime'] : '00:00',
                        'close_time' => $day['isOpen'] ? $day['closeTime'] : '00:00',
                        'is_active' => $day['isOpen'],
                        'is_biweekly' => false
                    ]
                );
            }
            
            // Handle biweekly Saturday separately
            if ($request->saturdaySchedule['enabled']) {
                OpeningHour::updateOrCreate(
                    ['day_of_week' => 6], // 6 = Saturday
                    [
                        'open_time' => $request->saturdaySchedule['openTime'],
                        'close_time' => $request->saturdaySchedule['closeTime'],
                        'is_active' => true,
                        'is_biweekly' => true
                    ]
                );
            } else {
                // If biweekly Saturday is disabled, update it normally from the weekly schedule
                $saturday = collect($request->weeklySchedule)->where('day', 'Saturday')->first();
                if ($saturday) {
                    OpeningHour::updateOrCreate(
                        ['day_of_week' => 6],
                        [
                            'open_time' => $saturday['isOpen'] ? $saturday['openTime'] : '00:00',
                            'close_time' => $saturday['isOpen'] ? $saturday['closeTime'] : '00:00',
                            'is_active' => $saturday['isOpen'],
                            'is_biweekly' => false
                        ]
                    );
                }
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Opening hours updated successfully.',
                'weeklySchedule' => $request->weeklySchedule,
                'saturdaySchedule' => $request->saturdaySchedule
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update opening hours: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(OpeningHour $openingHour): JsonResponse
    {
        return response()->json($openingHour->load('breakTimes'));
    }

    public function update(Request $request, OpeningHour $openingHour): JsonResponse
    {
        $validated = $request->validate([
            'open_time' => 'sometimes|required|date_format:H:i',
            'close_time' => 'sometimes|required|date_format:H:i|after:open_time',
            'is_active' => 'boolean',
            'is_biweekly' => 'boolean',
            'break_times' => 'nullable|array',
            'break_times.*.id' => 'nullable|exists:break_times,id',
            'break_times.*.start_time' => 'required_with:break_times|date_format:H:i',
            'break_times.*.end_time' => 'required_with:break_times|date_format:H:i|after:break_times.*.start_time',
        ]);

        $openingHour->update([
            'open_time' => $validated['open_time'] ?? $openingHour->open_time,
            'close_time' => $validated['close_time'] ?? $openingHour->close_time,
            'is_active' => $validated['is_active'] ?? $openingHour->is_active,
            'is_biweekly' => $validated['is_biweekly'] ?? $openingHour->is_biweekly,
        ]);

        // Update break times if provided
        if (!empty($validated['break_times'])) {
            // Remove existing break times not in the updated list
            $updatedIds = collect($validated['break_times'])->pluck('id')->filter()->all();
            $openingHour->breakTimes()->whereNotIn('id', $updatedIds)->delete();

            foreach ($validated['break_times'] as $breakTime) {
                if (!empty($breakTime['id'])) {
                    // Update existing break time
                    BreakTime::where('id', $breakTime['id'])->update([
                        'start_time' => $breakTime['start_time'],
                        'end_time' => $breakTime['end_time'],
                    ]);
                } else {
                    // Create new break time
                    $openingHour->breakTimes()->create([
                        'start_time' => $breakTime['start_time'],
                        'end_time' => $breakTime['end_time'],
                    ]);
                }
            }
        }

        return response()->json($openingHour->load('breakTimes'));
    }

    public function destroy(OpeningHour $openingHour): JsonResponse
    {
        $openingHour->delete();
        return response()->json(null, 204);
    }

    public function status(): JsonResponse
    {
        $isOpen = OpeningHour::isOpenNow();
        $nextOpenDate = OpeningHour::getNextOpenDate();

        return response()->json([
            'is_open' => $isOpen,
            'next_open_date' => $nextOpenDate,
            'current_time' => Carbon::now(),
        ]);
    }

    public function checkDate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $validated['date']);
        $dayOfWeek = $date->dayOfWeek;

        $openingHour = OpeningHour::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with('breakTimes')
            ->first();

        if (!$openingHour) {
            return response()->json([
                'is_open' => false,
                'message' => 'The bakery is closed on this day.',
            ]);
        }

        // If it's Saturday and biweekly, check if it's an "open" Saturday
        if ($dayOfWeek == 6 && $openingHour->is_biweekly) {
            $weekNumber = $date->weekOfYear;
            if ($weekNumber % 2 !== 0) {
                return response()->json([
                    'is_open' => false,
                    'message' => 'The bakery is closed on this Saturday.',
                ]);
            }
        }

        // Calculate typical busy days
        $popularityMessage = null;
        if ($dayOfWeek == 6) {  // Saturday
            $popularityMessage = "Saturdays are usually busy!";
        } elseif ($dayOfWeek == 1) {  // Monday
            $popularityMessage = "Mondays tend to be quieter.";
        }

        return response()->json([
            'is_open' => true,
            'open_time' => $openingHour->open_time,
            'close_time' => $openingHour->close_time,
            'break_times' => $openingHour->breakTimes,
            'popularity_insight' => $popularityMessage,
        ]);
    }
} 