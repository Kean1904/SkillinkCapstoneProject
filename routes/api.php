<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\JobApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\CitizenApiController;

/*
|--------------------------------------------------------------------------
| API Routes for SKILLINK Mobile App Sync & Web Integration
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/password/forgot', [AuthApiController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthApiController::class, 'resetPassword']);
Route::get('/mail-health', function () {
    $brevoKey = config('services.brevo.key') ?: env('BREVO_API_KEY');
    $sender = config('services.brevo.sender_email') ?: env('BREVO_SENDER_EMAIL');
    return response()->json([
        'status' => 'ok',
        'brevo_key_prefix' => substr($brevoKey ?? '', 0, 15) . '...',
        'brevo_sender' => $sender,
    ]);
});

// Citizens & Workers
Route::get('/workers', [CitizenApiController::class, 'getWorkers']);
Route::get('/users', [CitizenApiController::class, 'getUsers']);

// Jobs & Applications
Route::get('/jobs', [JobApiController::class, 'index']);
Route::post('/jobs', [JobApiController::class, 'store']);
Route::post('/jobs/{id}/apply', [JobApiController::class, 'apply']);

// Direct Bookings & 4-Stage Stepper
Route::get('/bookings', [BookingApiController::class, 'index']);
Route::post('/bookings', [BookingApiController::class, 'store']);
Route::put('/bookings/{id}/status', [BookingApiController::class, 'updateStatus']);

// Ratings & Official Complaints
Route::post('/reviews', [CitizenApiController::class, 'postReview']);
Route::post('/complaints', [CitizenApiController::class, 'postComplaint']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthApiController::class, 'logout']);
});