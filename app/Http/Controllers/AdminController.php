<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalRevenue' => Payment::query()->where('status_payments', 'settlement')->sum('amount') ?? 0,
            'activeBookings' => Booking::query()->where('status_bookings', 'Paid')->count('id'),
            'newUsers' => User::query()->where('created_at', '>=', now()->subMonth())->count('id'),
            'conversionRate' => 68.5,
            'recentBookings' => Booking::query()->with(['user', 'schedule.field'])->latest()->take(5)->get(),
            'totalFields' => Field::query()->count('id'),
            'totalUsers' => User::query()->count('id'),
            'todayBookings' => Booking::query()->whereDate('created_at', '=', today(), 'and')->count('id'),
            'todayRevenue' => Payment::query()->whereDate('created_at', '=', today(), 'and')->where('status_payments', 'settlement')->sum('amount') ?? 0,
            'pendingBookings' => Booking::query()->where('status_bookings', 'Pending')->count('id'),
        ]);
    }

    public function fields()
    {
        $fields = Field::query()->with('facilities')->latest()->get();
        return view('admin.fields', compact('fields'));
    }

    public function users()
    {
        $users = User::query()->latest()->get();
        return view('admin.users', [
            'users' => $users,
            'totalUsers' => $users->count(),
            'activeNow' => User::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'banned' => 0, 
        ]);
    }

    public function bookings()
    {
        $bookings = Booking::query()->with(['user', 'schedule.field', 'payment'])->latest()->get();
        return view('admin.bookings', compact('bookings'));
    }
}