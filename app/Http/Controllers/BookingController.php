<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function history()
    {
        // Auto expire booking pending > 30 menit
        $expiredBookings = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('status_bookings', 'Pending')
            ->where('created_at', '<=', now()->subMinutes(30))
            ->get();

        foreach ($expiredBookings as $booking) {

            // Ubah status booking
            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => 'Expired - pembayaran melebihi 30 menit'
            ]);

            // Ubah status payment
            if ($booking->payment) {
                $booking->payment->update([
                    'status_payments' => 'expired'
                ]);
            }

            // Buka kembali jadwal
            $this->freeSchedules($booking);
        }

        $bookings = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.history', compact('bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:schedules,id_schedules',
            'play_date' => 'required|date|after_or_equal:today',
            'subcourt_name' => 'required|string',
        ]);

        $schedules = Schedule::query()
            ->with('field')
            ->whereIn('id_schedules', $request->schedule_ids)
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $totalPrice = 0;
        $firstFieldId = $schedules->first()->field->id_fields;

        foreach ($schedules as $schedule) {
            $status = strtolower(trim($schedule->status_schedules));
            
            if (!in_array($status, ['tersedia', 'available', '0'])) {
                return back()->with('error', 'Maaf, salah satu jadwal yang Anda pilih baru saja dibooking oleh orang lain.');
            }

            if ($schedule->field->id_fields !== $firstFieldId) {
                return back()->with('error', 'Semua jadwal harus dari lapangan yang sama.');
            }


            $totalPrice += $schedule->field->price_per_hour;
        }

        $firstSchedule = $schedules->first();
        $bookingCode = '#AC-' . strtoupper(Str::random(6));

        $booking = Booking::query()->create([
            'user_id' => Auth::id(),
            'schedule_id' => $firstSchedule->id_schedules,
            'schedule_ids' => $request->schedule_ids,
            'subcourt_name' => $request->subcourt_name,
            'booking_code' => $bookingCode,
            'total_price' => $totalPrice,
            'status_bookings' => 'Pending',
            'play_date' => $request->play_date,
        ]);

        // Menggunakan status 'Booked' agar sesuai dengan standar ENUM umumnya
        Schedule::query()
            ->whereIn('id_schedules', $request->schedule_ids)
            ->update(['status_schedules' => 'Booked']);

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $midtransOrderId = $bookingCode . '-' . time();

        $params = array(
            'transaction_details' => array(
                'order_id' => $midtransOrderId,
                'gross_amount' => $totalPrice,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name_users,
                'email' => Auth::user()->email,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        Payment::query()->create([
            'booking_id' => $booking->id_bookings,
            'midtrans_order_id' => $midtransOrderId,
            'amount' => $totalPrice,
            'status_payments' => 'pending',
            'snap_token' => $snapToken,
        ]);

        return redirect()->route('booking.checkout', $booking->id_bookings);
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::query()
                          ->with('schedule.field')
                          ->where('id_bookings', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        $request->validate([
            'cancel_reason' => 'required|string|max:255'
        ]);

        // Cegah cancel booking yang sudah dicancel
        if ($booking->status_bookings === 'Cancelled') {
            return back()->with('error', 'Booking ini sudah dibatalkan sebelumnya.');
        }

        try {
            $this->freeSchedules($booking);
        } catch (\Exception $e) {
            Log::error('Cancel freeSchedules error: ' . $e->getMessage(), [
                'booking_id' => $booking->id_bookings
            ]);
            // Tetap lanjutkan cancel meskipun free schedules gagal
        }

        // Jika status Pending, boleh cancel kapan saja (tanpa H-3)
        if ($booking->status_bookings === 'Pending') {
            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason
            ]);
            return back()->with('success', 'Booking berhasil dibatalkan.');
        }

        // Jika status Paid, hanya bisa cancel jika masih H-3 atau lebih
        if ($booking->status_bookings === 'Paid') {
            $playDate = Carbon::parse($booking->play_date);
            $today = Carbon::now();
            $daysDifference = $today->diffInDays($playDate, false);

            if ($daysDifference < 3) {
                return back()->with('error', 'Pembatalan gagal. Booking yang sudah dibayar hanya dapat dibatalkan maksimal H-3 sebelum jadwal bermain.');
            }

            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason
            ]);
            return back()->with('success', 'Booking berhasil dibatalkan.');
        }

        return back()->with('error', 'Booking tidak dapat dibatalkan.');
    }

    /**
     * Free the schedules associated with a booking.
     */
    private function freeSchedules($booking)
    {
        // Use schedule_ids JSON if available (new bookings), fall back to single schedule_id
        $scheduleIdsToFree = $booking->schedule_ids;

        if (!empty($scheduleIdsToFree) && is_array($scheduleIdsToFree)) {
            Schedule::query()
                ->whereIn('id_schedules', $scheduleIdsToFree)
                ->update(['status_schedules' => 'Available']);
            return;
        }

        // Fallback for old records with only schedule_id
        $schedule = $booking->schedule;
        if (!$schedule || !$schedule->field) {
            return;
        }

        $pricePerHour = $schedule->field->price_per_hour;
        $duration = ($pricePerHour <= 0) ? 1 : round($booking->total_price / $pricePerHour);

        $schedulesToFree = Schedule::query()
            ->where('field_id', $schedule->field_id)
            ->where('date', $booking->play_date)
            ->where('start_time', '>=', $schedule->start_time)
            ->orderBy('start_time')
            ->limit($duration)
            ->pluck('id_schedules');

        if ($schedulesToFree->isEmpty()) {
            Log::warning('FREE SCHEDULES: no schedules found', [
                'booking' => $booking->id_bookings
            ]);
            return;
        }

        Schedule::query()
            ->whereIn('id_schedules', $schedulesToFree)
            ->update(['status_schedules' => 'Available']);
    }

    public function invoice($id)
    {
        $booking = Booking::query()
            ->with(['schedule.field', 'payment', 'user'])
            ->where('id_bookings', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status_bookings !== 'Paid') {
            return redirect()->route('user.history')->with('error', 'Invoice hanya tersedia untuk booking yang sudah dibayar.');
        }

        return view('user.invoice', compact('booking'));
    }

    public function checkout($id)
    {
        $booking = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('id_bookings', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $snapToken = $booking->payment?->snap_token;

        if (!$snapToken) {
            return redirect()->route('user.history')->with('error', 'Token pembayaran tidak ditemukan. Silakan lakukan booking ulang.');
        }

        return view('fields.checkout', compact('booking', 'snapToken'));
    }

    public function show($id)
    {
        $booking = Booking::with([
            'schedule.field',
            'payment',
            'user'
        ])->findOrFail($id);

        return view('user.booking-detail', compact('booking'));
    }

}
