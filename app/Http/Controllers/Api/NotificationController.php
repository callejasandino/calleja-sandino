<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\OpeningHour;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Check if the email is already subscribed
        $existingNotification = Notification::where('email', $validated['email'])
            ->where('is_active', true)
            ->first();

        if ($existingNotification) {
            return response()->json([
                'message' => 'This email is already subscribed to notifications.',
            ], 422);
        }

        // Get the next opening date for the notification
        $nextOpenDate = OpeningHour::getNextOpenDate();

        $notification = Notification::create([
            'email' => $validated['email'],
            'next_notification_date' => $nextOpenDate,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'You have been subscribed to notifications successfully.',
            'notification' => $notification,
        ], 201);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $notification = Notification::where('email', $validated['email'])
            ->where('is_active', true)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'This email is not subscribed to notifications.',
            ], 404);
        }

        $notification->update([
            'is_active' => false,
        ]);

        return response()->json([
            'message' => 'You have been unsubscribed from notifications successfully.',
        ]);
    }

    // This would be called by a scheduled task to send notifications
    public function sendNotifications(): JsonResponse
    {
        $now = Carbon::now();
        
        // Find notifications that are due
        $notifications = Notification::where('is_active', true)
            ->where('next_notification_date', '<=', $now)
            ->get();
            
        $count = 0;
        
        foreach ($notifications as $notification) {
            // In a real application, we would send an email here
            // Mail::to($notification->email)->send(new StoreOpeningNotification());
            
            // Update the next notification date to prevent duplicate notifications
            $notification->update([
                'next_notification_date' => null,
            ]);
            
            $count++;
        }
        
        return response()->json([
            'message' => "Sent {$count} notifications.",
        ]);
    }
} 