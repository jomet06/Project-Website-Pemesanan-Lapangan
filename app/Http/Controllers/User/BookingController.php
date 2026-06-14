<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Payment;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class BookingController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    // =====================
    // SEARCH & BROWSE FIELDS
    // =====================

    public function searchFields(Request $request)
    {
        $query = Field::with('facilities')->active();

        if ($request->filled('type')) {
            $query->where('type_fields', $request->type);
        }
        if ($request->filled('min_price')) {
            $query->where('price_per_hour', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_hour', '<=', $request->max_price);
        }
        if ($request->filled('date')) {
            $query->whereHas('schedules', function ($q) use ($request) {
                $q->where('date', $request->date)
                  ->where('status_schedules', 'available');
            });
        }

        $fields = $query->paginate(9);
        $types  = Field::active()->distinct()->pluck('type_fields')->filter();

        return view('user.fields.search', compact('fields', 'types'));
    }

    public function showField(Field $field)
    {
        $field->load('facilities');
        $schedules = Schedule::where('field_id', $field->id_fields)
            ->where('date', '>=', today())
            ->where('status_schedules', 'available')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('date');

        return view('user.fields.show', compact('field', 'schedules'));
    }

    // =====================
    // BOOKING PROCESS
    // =====================

    public function confirmBooking(Request $request, Field $field)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id_schedules',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        if ($schedule->status_schedules !== 'available') {
            return back()->withErrors(['schedule' => 'Jadwal ini sudah tidak tersedia.']);
        }

        $duration   = $schedule->duration_hours;
        $totalPrice = $duration * $field->price_per_hour;

        return view('user.bookings.confirm', compact('field', 'schedule', 'totalPrice'));
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
            return back()->withErrors(['schedule' => 'Jadwal ini sudah tidak tersedia.']);
        }

        try {
            DB::beginTransaction();

            $duration   = $schedule->duration_hours;
            $totalPrice = $duration * $field->price_per_hour;

            // Create Booking
            $booking = Booking::create([
                'user_id'        => auth()->id(),
                'schedule_id'    => $schedule->id_schedules,
                'booking_code'   => Booking::generateCode(),
                'total_price'    => $totalPrice,
                'status_bookings'=> 'pending',
                'play_date'      => $schedule->date,
            ]);

            // Mark schedule as booked
            $schedule->update(['status_schedules' => 'booked']);

            // Create Midtrans Payment
            $orderId = 'ORDER-' . $booking->id_bookings . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => (int) $totalPrice,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name_users,
                    'email'      => auth()->user()->email,
                ],
                'item_details' => [
                    [
                        'id'       => $field->id_fields,
                        'price'    => (int) $field->price_per_hour,
                        'quantity' => (int) $duration,
                        'name'     => $field->name_fields . ' (' . $schedule->start_time . ' - ' . $schedule->end_time . ')',
                    ],
                ],
                'callbacks' => [
                    'finish' => route('user.bookings.success', $booking->id_bookings),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Create Payment record
            Payment::create([
                'booking_id'          => $booking->id_bookings,
                'midtrans_order_id'   => $orderId,
                'amount'              => $totalPrice,
                'status_payments'     => 'pending',
                'snap_token'          => $snapToken,
            ]);

            DB::commit();

            return redirect()->route('user.bookings.payment', $booking->id_bookings)
                ->with('snap_token', $snapToken);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function showPayment(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $payment = $booking->payment;
        return view('user.bookings.payment', compact('booking', 'payment'));
    }

    public function paymentCallback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $orderId   = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;

        // Verify signature
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signature !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('midtrans_order_id', $orderId)->firstOrFail();
        $booking = $payment->booking;

        $transactionStatus = $request->transaction_status;

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $payment->update([
                'status_payments'          => 'success',
                'midtrans_transaction_id'  => $request->transaction_id,
                'payment_method'           => $request->payment_type,
                'paid_at'                  => now(),
            ]);
            $booking->update(['status_bookings' => 'confirmed']);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $payment->update(['status_payments' => $transactionStatus === 'expire' ? 'expired' : $transactionStatus]);
            $booking->update(['status_bookings' => 'cancelled']);
            // Free up schedule
            $booking->schedule->update(['status_schedules' => 'available']);
        }

        return response()->json(['message' => 'OK']);
    }

    public function success(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) abort(403);
        $booking->load(['schedule.field', 'payment']);
        return view('user.bookings.success', compact('booking'));
    }

    // =====================
    // BOOKING HISTORY
    // =====================

    public function history()
    {
        $bookings = Booking::with(['schedule.field', 'payment'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.bookings.history', compact('bookings'));
    }

    // =====================
    // CANCEL BOOKING
    // =====================

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) abort(403);

        if (!$booking->canBeCancelled()) {
            return back()->withErrors(['cancel' => 'Pemesanan tidak dapat dibatalkan. Hanya bisa dibatalkan minimal H-3 sebelum jadwal bermain.']);
        }

        $request->validate([
            'cancel_reason' => 'required|string|min:10|max:500',
        ]);

        DB::transaction(function () use ($booking, $request) {
            $booking->update([
                'status_bookings' => 'cancelled',
                'cancelled_at'    => now(),
                'cancel_reason'   => $request->cancel_reason,
            ]);

            // Free up schedule
            $booking->schedule->update(['status_schedules' => 'available']);

            // Update payment if exists
            if ($booking->payment) {
                $booking->payment->update(['status_payments' => 'cancel']);
            }
        });

        return redirect()->route('user.bookings.history')
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}