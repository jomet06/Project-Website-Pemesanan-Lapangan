<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Payment;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['schedule.field', 'payment'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id_schedules',
            'field_id'    => 'required|exists:fields,id_fields',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        $field    = Field::findOrFail($request->field_id);

        if ($schedule->status_schedules !== 'available') {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak tersedia.'], 422);
        }

        try {
            DB::beginTransaction();

            $duration   = $schedule->duration_hours;
            $totalPrice = $duration * $field->price_per_hour;

            $booking = Booking::create([
                'user_id'         => auth()->id(),
                'schedule_id'     => $schedule->id_schedules,
                'booking_code'    => Booking::generateCode(),
                'total_price'     => $totalPrice,
                'status_bookings' => 'pending',
                'play_date'       => $schedule->date,
            ]);

            $schedule->update(['status_schedules' => 'booked']);

            Payment::create([
                'booking_id'        => $booking->id_bookings,
                'midtrans_order_id' => 'ORDER-' . $booking->id_bookings . '-' . time(),
                'amount'            => $totalPrice,
                'status_payments'   => 'pending',
            ]);

            DB::commit();

            return response()->json(['success' => true, 'data' => $booking->load(['schedule.field', 'payment'])], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $booking->load(['schedule.field', 'payment']);
        return response()->json(['success' => true, 'data' => $booking]);
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if (!$booking->canBeCancelled()) {
            return response()->json(['success' => false, 'message' => 'Pemesanan tidak dapat dibatalkan (H-3).'], 422);
        }

        $request->validate(['cancel_reason' => 'required|string|min:5']);

        DB::transaction(function () use ($booking, $request) {
            $booking->update([
                'status_bookings' => 'cancelled',
                'cancelled_at'    => now(),
                'cancel_reason'   => $request->cancel_reason,
            ]);
            $booking->schedule->update(['status_schedules' => 'available']);
            if ($booking->payment) {
                $booking->payment->update(['status_payments' => 'cancel']);
            }
        });

        return response()->json(['success' => true, 'message' => 'Pemesanan berhasil dibatalkan.']);
    }
}
