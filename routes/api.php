<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OpeningHourController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Opening Hours
Route::get('/status', [OpeningHourController::class, 'status']);
Route::post('/check-date', [OpeningHourController::class, 'checkDate']);

// Admin Routes (would typically be protected in a real app)
Route::prefix('admin')->group(function () {
    Route::apiResource('opening-hours', OpeningHourController::class);
    Route::post('/opening-hours/bulk', [OpeningHourController::class, 'bulkUpdate']);
    Route::get('/notifications/send', [NotificationController::class, 'sendNotifications']);
});

// Notifications
Route::post('/notifications/subscribe', [NotificationController::class, 'subscribe']);
Route::post('/notifications/unsubscribe', [NotificationController::class, 'unsubscribe']);

// Appointments
Route::apiResource('appointments', AppointmentController::class);
Route::post('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots']); 