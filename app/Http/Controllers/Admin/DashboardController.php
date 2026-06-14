<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_fields'   => Field::count(),
            'total_users'    => User::where('role', 'user')->count(),
            'total_bookings' => Booking::count(),
            'pending'        => Booking::where('status_bookings', 'pending')->count(),
            'confirmed'      => Booking::where('status_bookings', 'confirmed')->count(),
            'revenue'        => Payment::where('status_payments', 'success')->sum('amount'),
        ];

        $recentBookings = Booking::with(['user', 'schedule.field'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}