<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function history()
    {
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
        $expectedStartTime = null;

        foreach ($schedules as $schedule) {
            $status = strtolower(trim($schedule->status_schedules));
            
            if (!in_array($status, ['tersedia', 'available', '0'])) {
                return back()->with('error', 'Maaf, salah satu jadwal yang Anda pilih baru saja dibooking oleh orang lain.');
            }

            if ($schedule->field->id_fields !== $firstFieldId) {
                return back()->with('error', 'Semua jadwal harus dari lapangan yang sama.');
            }

            if ($expectedStartTime && $schedule->start_time != $expectedStartTime) {
                return back()->with('error', 'Jadwal yang dipilih harus berurutan tanpa jeda.');
            }

            $totalPrice += $schedule->field->price_per_hour;
            $expectedStartTime = Carbon::parse($schedule->start_time)->addHour()->format('H:i:s');
        }

        $firstSchedule = $schedules->first();
        $bookingCode = '#AC-' . strtoupper(Str::random(6));

        $booking = Booking::query()->create([
            'user_id' => Auth::id(),
            'schedule_id' => $firstSchedule->id_schedules, 
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

        $playDate = Carbon::parse($booking->play_date);
        $today = Carbon::now();
        $daysDifference = $today->diffInDays($playDate, false);

        if ($daysDifference < 3) {
            return back()->with('error', 'Pembatalan gagal. Booking hanya dapat dibatalkan maksimal H-3 sebelum jadwal bermain.');
        }

        $pricePerHour = $booking->schedule->field->price_per_hour;
        $duration = ($pricePerHour <= 0) ? 1 : round($booking->total_price / $pricePerHour);

        $schedulesToFree = Schedule::query()
            ->where('field_id', $booking->schedule->field_id)
            ->where('date', $booking->play_date) 
            ->where('start_time', '>=', $booking->schedule->start_time)
            ->orderBy('start_time')
            ->limit($duration)
            ->pluck('id_schedules');

        $booking->update([
            'status_bookings' => 'Cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => $request->cancel_reason
        ]);

        if ($schedulesToFree->isNotEmpty()) {
            Schedule::query()
                ->whereIn('id_schedules', $schedulesToFree)
                ->update(['status_schedules' => 'Tersedia']);
        }

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    public function checkout($id)
    {
        return redirect()->route('user.history')->with('success', 'Checkout booking #' . $id . ' siap diproses.');
    }
}