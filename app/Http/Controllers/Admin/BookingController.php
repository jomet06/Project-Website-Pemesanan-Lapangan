<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'schedule.field', 'payment'])->latest();

        if ($request->filled('status')) {
            $query->where('status_bookings', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%$search%")
                  ->orWhereHas('user', fn($u) => $u->where('name_users', 'like', "%$search%"))
                  ->orWhereHas('schedule.field', fn($f) => $f->where('name_fields', 'like', "%$search%"));
            });
        }

        $bookings = $query->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'schedule.field', 'payment']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status_bookings' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update(['status_bookings' => $request->status_bookings]);

        // If marking complete, update schedule too
        if ($request->status_bookings === 'completed') {
            $booking->schedule->update(['status_schedules' => 'booked']);
        }

        return back()->with('success', 'Status booking berhasil diperbarui!');
    }
}