<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\OpeningHour;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(): JsonResponse
    {
        $appointments = Appointment::orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();
            
        return response()->json($appointments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'notes' => 'nullable|string',
            'appointment_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // Check if the bakery is open on the selected date and time
        $date = Carbon::createFromFormat('Y-m-d', $validated['appointment_date']);
        $dayOfWeek = $date->dayOfWeek;
        $time = $validated['appointment_time'];

        $openingHour = OpeningHour::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with('breakTimes')
            ->first();

        if (!$openingHour) {
            return response()->json([
                'message' => 'The bakery is closed on this day.',
            ], 422);
        }

        // If it's Saturday and biweekly, check if it's an "open" Saturday
        if ($dayOfWeek == 6 && $openingHour->is_biweekly) {
            $weekNumber = $date->weekOfYear;
            if ($weekNumber % 2 !== 0) {
                return response()->json([
                    'message' => 'The bakery is closed on this Saturday.',
                ], 422);
            }
        }

        // Check if the appointment time is within opening hours
        if ($time < $openingHour->open_time || $time > $openingHour->close_time) {
            return response()->json([
                'message' => 'The appointment time is outside of opening hours.',
            ], 422);
        }

        // Check if the appointment time is during a break
        foreach ($openingHour->breakTimes as $breakTime) {
            if ($time >= $breakTime->start_time && $time <= $breakTime->end_time) {
                return response()->json([
                    'message' => 'The appointment time is during a break period.',
                ], 422);
            }
        }

        // Check for existing appointments at the same time
        $existingAppointment = Appointment::where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'message' => 'This time slot is already booked. Please select another time.',
            ], 422);
        }

        $appointment = Appointment::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'notes' => $validated['notes'] ?? null,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
        ]);

        return response()->json([
            'message' => 'Appointment scheduled successfully.',
            'appointment' => $appointment,
        ], 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        return response()->json($appointment);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email',
            'notes' => 'nullable|string',
            'appointment_date' => 'sometimes|required|date_format:Y-m-d|after_or_equal:today',
            'appointment_time' => 'sometimes|required|date_format:H:i',
        ]);

        // If date or time is being updated, validate against opening hours
        if (isset($validated['appointment_date']) || isset($validated['appointment_time'])) {
            $date = Carbon::createFromFormat('Y-m-d', $validated['appointment_date'] ?? $appointment->appointment_date);
            $dayOfWeek = $date->dayOfWeek;
            $time = $validated['appointment_time'] ?? $appointment->appointment_time;

            $openingHour = OpeningHour::where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->with('breakTimes')
                ->first();

            if (!$openingHour) {
                return response()->json([
                    'message' => 'The bakery is closed on this day.',
                ], 422);
            }

            // If it's Saturday and biweekly, check if it's an "open" Saturday
            if ($dayOfWeek == 6 && $openingHour->is_biweekly) {
                $weekNumber = $date->weekOfYear;
                if ($weekNumber % 2 !== 0) {
                    return response()->json([
                        'message' => 'The bakery is closed on this Saturday.',
                    ], 422);
                }
            }

            // Check if the appointment time is within opening hours
            if ($time < $openingHour->open_time || $time > $openingHour->close_time) {
                return response()->json([
                    'message' => 'The appointment time is outside of opening hours.',
                ], 422);
            }

            // Check if the appointment time is during a break
            foreach ($openingHour->breakTimes as $breakTime) {
                if ($time >= $breakTime->start_time && $time <= $breakTime->end_time) {
                    return response()->json([
                        'message' => 'The appointment time is during a break period.',
                    ], 422);
                }
            }

            // Check for existing appointments at the same time (excluding this one)
            $existingAppointment = Appointment::where('id', '!=', $appointment->id)
                ->where('appointment_date', $validated['appointment_date'] ?? $appointment->appointment_date)
                ->where('appointment_time', $validated['appointment_time'] ?? $appointment->appointment_time)
                ->first();

            if ($existingAppointment) {
                return response()->json([
                    'message' => 'This time slot is already booked. Please select another time.',
                ], 422);
            }
        }

        $appointment->update([
            'name' => $validated['name'] ?? $appointment->name,
            'email' => $validated['email'] ?? $appointment->email,
            'notes' => $validated['notes'] ?? $appointment->notes,
            'appointment_date' => $validated['appointment_date'] ?? $appointment->appointment_date,
            'appointment_time' => $validated['appointment_time'] ?? $appointment->appointment_time,
        ]);

        return response()->json([
            'message' => 'Appointment updated successfully.',
            'appointment' => $appointment,
        ]);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $appointment->delete();
        
        return response()->json([
            'message' => 'Appointment cancelled successfully.',
        ]);
    }

    public function getAvailableSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $validated['date']);
        $dayOfWeek = $date->dayOfWeek;

        $openingHour = OpeningHour::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with('breakTimes')
            ->first();

        if (!$openingHour) {
            return response()->json([
                'available_slots' => [],
                'message' => 'The bakery is closed on this day.',
            ]);
        }

        // If it's Saturday and biweekly, check if it's an "open" Saturday
        if ($dayOfWeek == 6 && $openingHour->is_biweekly) {
            $weekNumber = $date->weekOfYear;
            if ($weekNumber % 2 !== 0) {
                return response()->json([
                    'available_slots' => [],
                    'message' => 'The bakery is closed on this Saturday.',
                ]);
            }
        }

        // Generate time slots at 30-minute intervals
        $startTime = Carbon::createFromFormat('H:i:s', $openingHour->open_time);
        $endTime = Carbon::createFromFormat('H:i:s', $openingHour->close_time);
        
        $slots = [];
        $current = $startTime->copy();
        
        while ($current < $endTime) {
            $timeSlot = $current->format('H:i');
            
            // Check if this slot is during a break
            $isDuringBreak = false;
            foreach ($openingHour->breakTimes as $breakTime) {
                $breakStart = Carbon::createFromFormat('H:i:s', $breakTime->start_time);
                $breakEnd = Carbon::createFromFormat('H:i:s', $breakTime->end_time);
                
                if ($current >= $breakStart && $current < $breakEnd) {
                    $isDuringBreak = true;
                    break;
                }
            }
            
            if (!$isDuringBreak) {
                // Check if this slot is already booked
                $isBooked = Appointment::where('appointment_date', $validated['date'])
                    ->where('appointment_time', $timeSlot)
                    ->exists();
                    
                if (!$isBooked) {
                    $slots[] = $timeSlot;
                }
            }
            
            $current->addMinutes(30);
        }
        
        return response()->json([
            'available_slots' => $slots,
        ]);
    }
} 