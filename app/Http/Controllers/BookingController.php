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
    public function history(Request $request)
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
                'cancel_reason' => 'Expired - payment exceeded 30 minutes'
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

        $tab = $request->query('tab', 'all');

        $query = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $bookings = $query->map(function($booking) {
            $computed = $booking->computed_status;
            
            if ($computed === 'Waiting for Payment') $booking->custom_status = 'waiting';
            elseif ($computed === 'Rescheduled') $booking->custom_status = 'reschedule';
            elseif ($computed === 'Canceled') $booking->custom_status = 'canceled';
            elseif ($computed === 'Done') $booking->custom_status = 'done';
            elseif ($computed === 'Paid') $booking->custom_status = 'paid';
            else $booking->custom_status = 'unknown';

            return $booking;
        });

        if ($tab !== 'all') {
            $bookings = $bookings->filter(fn($b) => $b->custom_status === $tab);
        }

        return view('user.history', compact('bookings', 'tab'));
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
            return back()->with('error', 'Schedule not found.');
        }

        $totalPrice = 0;
        $firstFieldId = $schedules->first()->field->id_fields;

        foreach ($schedules as $schedule) {
            $status = strtolower(trim($schedule->status_schedules));
            
            if (!in_array($status, ['tersedia', 'available', '0'])) {
                return back()->with('error', 'Sorry, one of the schedules you selected was just booked by someone else.');
            }

            if ($schedule->field->id_fields !== $firstFieldId) {
                return back()->with('error', 'All schedules must be from the same field.');
            }


            $totalPrice += $schedule->field->price_per_hour;
        }

        $firstSchedule = $schedules->first();
        $rescheduleId = session('reschedule_booking_id');

        if ($rescheduleId) {
            $oldBooking = Booking::findOrFail($rescheduleId);
            
            if ($totalPrice > $oldBooking->total_price) {
                return back()->with('error', 'The duration of the new schedule exceeds your reschedule limit. Select a duration equal to or less than Rp ' . number_format($oldBooking->total_price, 0, ',', '.') . ').');
            }

            $bookingCode = '#AC-RES-' . strtoupper(Str::random(4));
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'schedule_id' => $firstSchedule->id_schedules,
                'schedule_ids' => $request->schedule_ids,
                'subcourt_name' => $request->subcourt_name,
                'booking_code' => $bookingCode,
                'total_price' => $totalPrice,
                'status_bookings' => 'Paid',
                'play_date' => $request->play_date,
            ]);

            Schedule::whereIn('id_schedules', $request->schedule_ids)
                ->update(['status_schedules' => 'Booked']);

            if ($oldBooking->payment) {
                $newPayment = $oldBooking->payment->replicate();
                $newPayment->booking_id = $booking->id_bookings;
                $newPayment->midtrans_order_id = $bookingCode . '-' . time();
                $newPayment->save();
            }

            $oldBooking->update([
                'status_bookings' => 'Cancelled',
                'cancel_reason' => 'Rescheduled'
            ]);
            $this->freeSchedules($oldBooking);

            session()->forget('reschedule_booking_id');
            return redirect()->route('user.history', ['tab' => 'paid'])->with('success_reschedule', 'Schedule rescheduled successfully!');
        }

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

        if ($booking->status_bookings === 'Cancelled') {
            return back()->with('error', 'This booking has already been cancelled.');
        }

        try {
            $this->freeSchedules($booking);
        } catch (\Exception $e) {
            Log::error('Cancel freeSchedules error: ' . $e->getMessage(), [
                'booking_id' => $booking->id_bookings
            ]);
        }

        if ($booking->status_bookings === 'Pending') {
            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason
            ]);
            return back()->with('success_cancel', 'Booking has been canceled.');
        }

        if ($booking->status_bookings === 'Paid') {
            $playDate = Carbon::parse($booking->play_date);
            $today = Carbon::now();
            $daysDifference = $today->diffInDays($playDate, false);

            if ($daysDifference < 3) {
                return back()->with('error', 'Cancellation failed. Paid bookings can only be cancelled at least 3 days (H-3) before the play date.');
            }

            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason
            ]);
            return back()->with('success_cancel', 'Booking has been canceled and your money is refunded.');
        }

        return back()->with('error', 'Booking cannot be cancelled.');
    }

    public function reschedule(Request $request, $id)
    {
        $booking = Booking::query()
                          ->with('schedule.field')
                          ->where('id_bookings', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        if ($booking->status_bookings !== 'Paid') {
            return back()->with('error', 'Only paid bookings can be rescheduled.');
        }

        $playDate = Carbon::parse($booking->play_date);
        $today = Carbon::now();
        $daysDifference = $today->diffInDays($playDate, false);

        if ($daysDifference < 3 && !Carbon::parse($booking->play_date)->isPast()) {
            return back()->with('error', 'Reschedule failed. Can only be done at least 3 days (H-3) before the play date.');
        }

        session(['reschedule_booking_id' => $booking->id_bookings]);

        // Redirect to the specific field page so they can pick a new date/time
        return redirect()->route('fields.show', $booking->schedule->field_id ?? $booking->getSchedulesList()->first()->field_id)
            ->with('info', 'Select a new schedule. You are currently in Reschedule mode (Max Rp ' . number_format($booking->total_price, 0, ',', '.') . ').');
    }

    /**
     * Free the schedules associated with a booking.
     */
    private function freeSchedules($booking)
    {
        $scheduleIdsToFree = $booking->schedule_ids;

        if (!empty($scheduleIdsToFree) && is_array($scheduleIdsToFree)) {
            Schedule::query()
                ->whereIn('id_schedules', $scheduleIdsToFree)
                ->update(['status_schedules' => 'Available']);
            return;
        }

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
            return redirect()->route('user.history')->with('error', 'Invoice is only available for paid bookings.');
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
            return redirect()->route('user.history')->with('error', 'Payment token not found. Please try booking again.');
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
