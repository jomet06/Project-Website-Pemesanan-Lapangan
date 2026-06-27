<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Menampilkan riwayat booking milik user yang sedang login.
     */
    public function history(Request $request)
    {
        $query = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat booking berhasil diambil',
            'data'    => $query
        ], 200);
    }

    /**
     * Membuat booking baru dan mendapatkan token pembayaran Midtrans.
     */
    public function store(Request $request)
    {
        $request->validate([
            'schedule_ids'  => 'required|array|min:1',
            'schedule_ids.*'=> 'exists:schedules,id_schedules',
            'play_date'     => 'required|date|after_or_equal:today',
            'subcourt_name' => 'required|string',
        ]);

        $schedules = Schedule::query()
            ->with('field')
            ->whereIn('id_schedules', $request->schedule_ids)
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan.'], 404);
        }

        $totalPrice = 0;
        $firstFieldId = $schedules->first()->field->id_fields;

        foreach ($schedules as $schedule) {
            $status = strtolower(trim($schedule->status_schedules));
            
            if (!in_array($status, ['tersedia', 'available', '0'])) {
                return response()->json(['success' => false, 'message' => 'Maaf, jadwal sudah dipesan oleh orang lain.'], 400);
            }

            if ($schedule->field->id_fields !== $firstFieldId) {
                return response()->json(['success' => false, 'message' => 'Semua jadwal harus dari lapangan yang sama.'], 400);
            }

            $totalPrice += $schedule->field->price_per_hour;
        }

        $firstSchedule = $schedules->first();
        $bookingCode = '#AC-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'user_id'         => $request->user()->id,
            'schedule_id'     => $firstSchedule->id_schedules,
            'schedule_ids'    => $request->schedule_ids,
            'subcourt_name'   => $request->subcourt_name,
            'booking_code'    => $bookingCode,
            'total_price'     => $totalPrice,
            'status_bookings' => 'Pending',
            'play_date'       => $request->play_date,
        ]);

        // Tandai jadwal sebagai Booked
        Schedule::whereIn('id_schedules', $request->schedule_ids)
            ->update(['status_schedules' => 'Booked']);

        // Setup Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $midtransOrderId = $bookingCode . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->user()->name_users ?? $request->user()->name,
                'email' => $request->user()->email,
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        Payment::create([
            'booking_id' => $booking->id_bookings,
            'midtrans_order_id' => $midtransOrderId,
            'amount' => $totalPrice,
            'status_payments' => 'pending',
            'snap_token' => $snapToken,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat',
            'data'    => [
                'booking' => $booking,
                'snap_token' => $snapToken
            ]
        ], 201);
    }

    /**
     * Menampilkan detail booking.
     */
    public function show(Request $request, $id)
    {
        $booking = Booking::with(['schedule.field', 'payment'])->where('id_bookings', $id)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);
        }

        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail booking',
            'data'    => $booking
        ], 200);
    }

    /**
     * Membatalkan booking.
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:255'
        ]);

        $booking = Booking::with('schedule.field')->where('id_bookings', $id)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);
        }

        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($booking->status_bookings === 'Cancelled') {
            return response()->json(['success' => false, 'message' => 'Booking ini sudah dibatalkan.'], 400);
        }

        if ($booking->status_bookings === 'Paid') {
            $playDate = Carbon::parse($booking->play_date);
            $today = Carbon::now();
            $daysDifference = $today->diffInDays($playDate, false);

            if ($daysDifference < 3) {
                return response()->json(['success' => false, 'message' => 'Pembatalan gagal. Booking yang sudah dibayar hanya bisa dibatalkan maksimal H-3 sebelum tanggal main.'], 400);
            }
        }

        // Buka kembali jadwal
        if (!empty($booking->schedule_ids) && is_array($booking->schedule_ids)) {
            Schedule::whereIn('id_schedules', $booking->schedule_ids)
                ->update(['status_schedules' => 'Available']);
        }

        $booking->update([
            'status_bookings' => 'Cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => $request->cancel_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan.',
            'data'    => $booking
        ], 200);
    }
}
