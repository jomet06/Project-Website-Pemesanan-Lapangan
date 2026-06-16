<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingSchedule;
use App\Models\Field;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        $booking->load(['user', 'schedule.field', 'payment', 'bookingSchedules.schedule']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status_bookings' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update(['status_bookings' => $request->status_bookings]);

        if ($request->status_bookings === 'cancelled') {
            $booking->load('bookingSchedules');
            $booking->freeAllSchedules();
            if ($booking->payment) {
                $booking->payment->update(['status_payments' => 'cancel']);
            }
        }

        return back()->with('success', 'Status booking berhasil diperbarui!');
    }

    // ─────────────────────────────────────────
    // OFFLINE / WALK-IN BOOKING
    // ─────────────────────────────────────────

    public function createOffline(Request $request)
    {
        $fields    = Field::where('is_active', true)->orderBy('name_fields')->get();
        $users     = User::where('role', 'user')->orderBy('name_users')->get();
        $schedules = collect();
        $selectedField = null;

        if ($request->filled('field_id') && $request->filled('date')) {
            $selectedField = Field::find($request->field_id);
            if ($selectedField) {
                $schedules = Schedule::where('field_id', $request->field_id)
                    ->where('date', $request->date)
                    ->where('status_schedules', 'available')
                    ->orderBy('court_number')
                    ->orderBy('start_time')
                    ->get();
            }
        }

        return view('admin.bookings.create', compact('fields', 'users', 'schedules', 'selectedField'));
    }

    public function storeOffline(Request $request)
    {
        $request->validate([
            'schedule_id'    => 'required|exists:schedules,id_schedules',
            'customer_type'  => 'required|in:existing,walkin',
            'user_id'        => 'required_if:customer_type,existing|nullable|exists:users,id_users',
            'guest_name'     => 'required_if:customer_type,walkin|nullable|string|max:255',
            'guest_phone'    => 'nullable|string|max:20',
            'payment_method' => 'required|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        $schedule = Schedule::with('field')->findOrFail($request->schedule_id);

        if ($schedule->status_schedules !== 'available') {
            return back()->withErrors(['schedule_id' => 'Jadwal ini sudah tidak tersedia. Silakan pilih jadwal lain.']);
        }

        DB::beginTransaction();
        try {
            $field      = $schedule->field;
            $duration   = $schedule->duration_hours;
            $totalPrice = $duration * $field->price_per_hour;

            if ($request->customer_type === 'walkin') {
                $timestamp = time();
                $user = User::create([
                    'name_users' => $request->guest_name,
                    'username'   => 'walkin_' . $timestamp,
                    'email'      => 'walkin_' . $timestamp . '@guest.activecourt.local',
                    'password'   => Hash::make(Str::random(20)),
                    'role'       => 'user',
                ]);
            } else {
                $user = User::findOrFail($request->user_id);
            }

            $booking = Booking::create([
                'user_id'         => $user->id_users,
                'schedule_id'     => $schedule->id_schedules,
                'booking_code'    => Booking::generateCode(),
                'total_price'     => $totalPrice,
                'status_bookings' => 'confirmed',
                'play_date'       => $schedule->date,
            ]);

            BookingSchedule::create([
                'booking_id'  => $booking->id_bookings,
                'schedule_id' => $schedule->id_schedules,
            ]);

            $schedule->update(['status_schedules' => 'booked']);

            $orderId = 'OFFLINE-' . $booking->id_bookings . '-' . time();

            Payment::create([
                'booking_id'        => $booking->id_bookings,
                'midtrans_order_id' => $orderId,
                'amount'            => $totalPrice,
                'payment_method'    => $request->payment_method,
                'status_payments'   => 'success',
                'paid_at'           => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('success', 'Booking offline untuk ' . $user->name_users . ' berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
