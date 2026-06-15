<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;

// --- Guest & Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- Auth Routes ---
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::post('/login', [AuthController::class, 'loginSubmit'])->name('login.submit');
Route::post('/register', [AuthController::class, 'registerSubmit'])->name('register.submit');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::get('/auth/google', [AuthController::class, 'googleAuth'])->name('auth.google');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Field Explorations ---
Route::get('/fields', [FieldController::class, 'index'])->name('fields.index');
Route::get('/fields/{field}', [FieldController::class, 'show'])->name('fields.show');

// --- Booking Actions & User Dashboard ---
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::get('/booking/{id}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::get('/user/history', [BookingController::class, 'history'])->name('user.history');

// --- Admin Portal ---
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/fields', [AdminController::class, 'fields'])->name('fields');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
});