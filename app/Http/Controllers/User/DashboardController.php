<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total'     => Booking::where('user_id', $user->id_users)->count(),
            'confirmed' => Booking::where('user_id', $user->id_users)->where('status_bookings', 'confirmed')->count(),
            'pending'   => Booking::where('user_id', $user->id_users)->where('status_bookings', 'pending')->count(),
            'cancelled' => Booking::where('user_id', $user->id_users)->where('status_bookings', 'cancelled')->count(),
        ];

        $recentBookings = Booking::with(['schedule.field', 'payment'])
            ->where('user_id', $user->id_users)
            ->latest()
            ->limit(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recentBookings'));
    }
}