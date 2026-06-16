<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\FieldController as AdminField;
use App\Http\Controllers\Admin\ScheduleController as AdminSchedule;
use App\Http\Controllers\Admin\BookingController as AdminBooking;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\BookingController as UserBooking;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC / GUEST ROUTES
// ============================================================
Route::get('/', [GuestController::class, 'home'])->name('home');
Route::get('/fields', [GuestController::class, 'fields'])->name('guest.fields');
Route::get('/fields/{field}', [GuestController::class, 'showField'])->name('guest.fields.show');
Route::get('/about', [GuestController::class, 'about'])->name('guest.about');
Route::get('/contact', [GuestController::class, 'contact'])->name('guest.contact');

// ============================================================
// AUTH ROUTES (only for guests / non-authenticated)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================================
// MIDTRANS PAYMENT CALLBACK (no auth - called by Midtrans server)
// ============================================================
Route::post('/payment/callback', [UserBooking::class, 'paymentCallback'])->name('payment.callback');

// ============================================================
// USER ROUTES (authenticated + role: user)
// ============================================================
Route::middleware(['auth', 'role:user,admin'])->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/', [UserDashboard::class, 'index'])->name('dashboard');

    // Search & Browse Fields
    Route::get('/fields', [UserBooking::class, 'searchFields'])->name('fields.search');
    Route::get('/fields/{field}', [UserBooking::class, 'showField'])->name('fields.show');

    // Booking flow
    Route::post('/fields/{field}/confirm', [UserBooking::class, 'confirmBooking'])->name('bookings.confirm');
    Route::post('/bookings', [UserBooking::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/payment', [UserBooking::class, 'showPayment'])->name('bookings.payment');
    Route::get('/bookings/{booking}/success', [UserBooking::class, 'success'])->name('bookings.success');

    // History & Cancel
    Route::get('/history', [UserBooking::class, 'history'])->name('bookings.history');
    Route::post('/bookings/{booking}/cancel', [UserBooking::class, 'cancel'])->name('bookings.cancel');
});

// ============================================================
// ADMIN ROUTES (authenticated + role: admin)
// ============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Fields CRUD
    Route::resource('fields', AdminField::class);

    // Schedules CRUD
    Route::resource('schedules', AdminSchedule::class)->except(['show']);

    // Bookings Management
    Route::get('/bookings', [AdminBooking::class, 'index'])->name('bookings.index');
    Route::get('/bookings/offline/create', [AdminBooking::class, 'createOffline'])->name('bookings.offline.create');
    Route::post('/bookings/offline', [AdminBooking::class, 'storeOffline'])->name('bookings.offline.store');
    Route::get('/bookings/{booking}', [AdminBooking::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [AdminBooking::class, 'updateStatus'])->name('bookings.status');

    // User Management
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUser::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [AdminUser::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}', [AdminUser::class, 'destroy'])->name('users.destroy');
});