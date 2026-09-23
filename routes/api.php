<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\JobApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\CitizenApiController;
use App\Http\Controllers\DashboardStatsController;

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
Route::post('/user/consent', [AuthApiController::class, 'updateConsent']);
Route::post('/user/heartbeat', [CitizenApiController::class, 'heartbeat']);

// Live Stats Endpoint (for Mobile Dashboard synchronization)
Route::get('/jobs/stats', [DashboardStatsController::class, 'getLiveStats']);
Route::get('/dashboard/stats', [DashboardStatsController::class, 'getLiveStats']);


// Citizens & Workers
Route::get('/workers', [CitizenApiController::class, 'getWorkers']);
Route::get('/users', [CitizenApiController::class, 'getUsers']);

// Jobs & Applications
Route::get('/jobs', [JobApiController::class, 'index']);
Route::post('/jobs', [JobApiController::class, 'store']);
Route::post('/jobs/{id}/apply', [JobApiController::class, 'apply']);
Route::put('/jobs/{id}/status', [JobApiController::class, 'updateStatus']);

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