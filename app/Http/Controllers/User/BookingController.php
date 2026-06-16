<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingSchedule;
use App\Models\Field;
use App\Models\Payment;
use App\Models\Schedule;
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
                $q->where('date', $request->date)->where('status_schedules', 'available');
            });
        }
        if ($request->filled('search')) {
            $query->where('name_fields', 'like', '%' . $request->search . '%');
        }

        $fields = $query->paginate(9);
        $types  = Field::active()->distinct()->pluck('type_fields')->filter();

        return view('user.fields.search', compact('fields', 'types'));
    }

    public function showField(Field $field)
    {
        $field->load('facilities');

        $rawSchedules = Schedule::where('field_id', $field->id_fields)
            ->where('date', '>=', today())
            ->where('status_schedules', 'available')
            ->orderBy('date')
            ->orderBy('court_number')
            ->orderBy('start_time')
            ->get();

        $hasCourts = $rawSchedules->whereNotNull('court_number')->isNotEmpty();
        $courts    = $rawSchedules->pluck('court_number')->filter()->unique()->sort()->values();

        // Build JSON-friendly schedule data: {date: {court_or_"single": [{id,start,end}]}}
        $scheduleData = [];
        $dates        = [];

        foreach ($rawSchedules as $slot) {
            $date  = $slot->date->format('Y-m-d');
            $court = $hasCourts ? (string) $slot->court_number : 'single';

            if (!in_array($date, $dates)) {
                $dates[] = $date;
            }

            $scheduleData[$date][$court][] = [
                'id'    => $slot->id_schedules,
                'start' => substr($slot->start_time, 0, 5),
                'end'   => substr($slot->end_time, 0, 5),
            ];
        }

        return view('user.fields.show', compact('field', 'scheduleData', 'dates', 'hasCourts', 'courts'));
    }

    // =====================
    // BOOKING PROCESS
    // =====================

    public function confirmBooking(Request $request, Field $field)
    {
        $request->validate([
            'schedule_ids'   => 'required|array|min:1',
            'schedule_ids.*' => 'exists:schedules,id_schedules',
        ]);

        $schedules = Schedule::whereIn('id_schedules', $request->schedule_ids)
            ->where('status_schedules', 'available')
            ->orderBy('start_time')
            ->get();

        if ($schedules->count() !== count($request->schedule_ids)) {
            return back()->withErrors(['schedule' => 'Beberapa jadwal yang dipilih sudah tidak tersedia.']);
        }

        $totalHours  = $schedules->count();
        $totalPrice  = $totalHours * $field->price_per_hour;
        $courtNumber = $schedules->first()->court_number;

        return view('user.bookings.confirm', compact('field', 'schedules', 'totalPrice', 'courtNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_ids'   => 'required|array|min:1',
            'schedule_ids.*' => 'exists:schedules,id_schedules',
            'field_id'       => 'required|exists:fields,id_fields',
        ]);

        $schedules = Schedule::whereIn('id_schedules', $request->schedule_ids)
            ->where('status_schedules', 'available')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        if ($schedules->count() !== count($request->schedule_ids)) {
            return back()->withErrors(['schedule' => 'Jadwal sudah tidak tersedia. Silakan pilih ulang.']);
        }

        $field         = Field::findOrFail($request->field_id);
        $firstSchedule = $schedules->first();
        $lastSchedule  = $schedules->last();
        $totalHours    = $schedules->count();
        $totalPrice    = $totalHours * $field->price_per_hour;

        try {
            DB::beginTransaction();

            $booking = Booking::create([
                'user_id'         => auth()->id(),
                'schedule_id'     => $firstSchedule->id_schedules,
                'booking_code'    => Booking::generateCode(),
                'total_price'     => $totalPrice,
                'status_bookings' => 'pending',
                'play_date'       => $firstSchedule->date,
            ]);

            // Link all slots to this booking
            foreach ($schedules as $schedule) {
                BookingSchedule::create([
                    'booking_id'  => $booking->id_bookings,
                    'schedule_id' => $schedule->id_schedules,
                ]);
            }

            // Mark all slots as booked
            Schedule::whereIn('id_schedules', $request->schedule_ids)
                ->update(['status_schedules' => 'booked']);

            $orderId   = 'ORDER-' . $booking->id_bookings . '-' . time();
            $timeLabel = substr($firstSchedule->start_time, 0, 5) . '-' . substr($lastSchedule->end_time, 0, 5);
            $courtInfo = $firstSchedule->court_number ? ' (Court ' . $firstSchedule->court_number . ')' : '';

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
                        'quantity' => (int) $totalHours,
                        'name'     => $field->name_fields . $courtInfo . ' (' . $timeLabel . ')',
                    ],
                ],
                'callbacks' => [
                    'finish' => route('user.bookings.success', $booking->id_bookings),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            Payment::create([
                'booking_id'        => $booking->id_bookings,
                'midtrans_order_id' => $orderId,
                'amount'            => $totalPrice,
                'status_payments'   => 'pending',
                'snap_token'        => $snapToken,
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
        $serverKey   = config('midtrans.server_key');
        $orderId     = $request->order_id;
        $statusCode  = $request->status_code;
        $grossAmount = $request->gross_amount;

        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signature !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('midtrans_order_id', $orderId)->firstOrFail();
        $booking = $payment->booking;

        $transactionStatus = $request->transaction_status;

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $payment->update([
                'status_payments'         => 'success',
                'midtrans_transaction_id' => $request->transaction_id,
                'payment_method'          => $request->payment_type,
                'paid_at'                 => now(),
            ]);
            $booking->update(['status_bookings' => 'confirmed']);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $payment->update(['status_payments' => $transactionStatus === 'expire' ? 'expired' : $transactionStatus]);
            $booking->update(['status_bookings' => 'cancelled']);
            $booking->load('bookingSchedules');
            $booking->freeAllSchedules();
        }

        return response()->json(['message' => 'OK']);
    }

    public function success(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) abort(403);
        $booking->load(['schedule.field', 'payment', 'bookingSchedules.schedule']);
        return view('user.bookings.success', compact('booking'));
    }

    // =====================
    // BOOKING HISTORY
    // =====================

    public function history()
    {
        $bookings = Booking::with(['schedule.field', 'payment', 'bookingSchedules.schedule'])
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

            $booking->load('bookingSchedules');
            $booking->freeAllSchedules();

            if ($booking->payment) {
                $booking->payment->update(['status_payments' => 'cancel']);
            }
        });

        return redirect()->route('user.bookings.history')
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}
