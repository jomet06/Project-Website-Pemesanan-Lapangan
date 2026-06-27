<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\BookingController;

// Auth Route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Internal REST API for mobile & new frontend
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

    // Public routes
    Route::get('fields', [FieldController::class, 'index']);
    Route::get('fields/{id}', [FieldController::class, 'show']);
    Route::get('schedules', [ScheduleController::class, 'index']);

    // Protected routes (Requires Authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::post('bookings', [BookingController::class, 'store']);
        Route::get('bookings/history', [BookingController::class, 'history']);
        Route::get('bookings/{id}', [BookingController::class, 'show']);
        Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel']);
    });
});
