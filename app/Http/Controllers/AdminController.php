<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use App\Models\Payment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalRevenue' => Payment::query()
                ->where('status_payments', 'settlement')
                ->sum('amount') ?? 0,

            'activeBookings' => Booking::query()
                ->where('status_bookings', 'Paid')
                ->count(),

            'newUsers' => User::query()
                ->where('created_at', '>=', now()->subMonth())
                ->count(),

            'conversionRate' => 68.5,

            'recentBookings' => Booking::query()
                ->with(['user', 'schedule.field'])
                ->latest()
                ->take(5)
                ->get(),

            'totalFields' => Field::query()->count(),

            'totalUsers' => User::query()->count(),

            'todayBookings' => Booking::query()
                ->whereDate('created_at', today())
                ->count(),

            'todayRevenue' => Payment::query()
                ->whereDate('created_at', today())
                ->where('status_payments', 'settlement')
                ->sum('amount') ?? 0,

            'pendingBookings' => Booking::query()
                ->where('status_bookings', 'Pending')
                ->count(),
        ]);
    }

    // ==================== FIELDS ====================

    public function fields()
    {
        $fields = Field::query()->with('facilities')->latest()->get();
        return view('admin.fields', compact('fields'));
    }

    public function storeField(Request $request)
    {
        $request->validate([
            'name_fields' => 'required|string|max:255',
            'type_fields' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'sub_courts' => 'required|json',
        ]);

        $subCourts = json_decode($request->sub_courts, true);
        if (!is_array($subCourts) || empty($subCourts)) {
            return back()->with('error', 'Sub-courts harus berupa array JSON yang valid dengan minimal 1 item.');
        }

        Field::query()->create([
            'name_fields' => $request->name_fields,
            'type_fields' => $request->type_fields,
            'address' => $request->address,
            'description' => $request->description ?? '',
            'price_per_hour' => $request->price_per_hour,
            'capacity' => $request->capacity,
            'image' => $request->image ?? null,
            'sub_courts' => $subCourts,
        ]);

        return redirect()->route('admin.fields')->with('success', 'Lapangan berhasil ditambahkan.');
    }

    public function editField($id)
    {
        $field = Field::query()->findOrFail($id);
        return view('admin.field-edit', compact('field'));
    }

    public function updateField(Request $request, $id)
    {
        $field = Field::query()->findOrFail($id);

        $request->validate([
            'name_fields' => 'required|string|max:255',
            'type_fields' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'sub_courts' => 'required|json',
        ]);

        $subCourts = json_decode($request->sub_courts, true);
        if (!is_array($subCourts) || empty($subCourts)) {
            return back()->with('error', 'Sub-courts harus berupa array JSON yang valid dengan minimal 1 item.');
        }

        $field->update([
            'name_fields' => $request->name_fields,
            'type_fields' => $request->type_fields,
            'address' => $request->address,
            'description' => $request->description ?? '',
            'price_per_hour' => $request->price_per_hour,
            'capacity' => $request->capacity,
            'sub_courts' => $subCourts,
        ]);

        return redirect()->route('admin.fields')->with('success', 'Lapangan berhasil diperbarui.');
    }

    public function destroyField($id)
    {
        $field = Field::query()->with('schedules')->findOrFail($id);

        // Check if there are active schedules
        $activeSchedules = $field->schedules()->where('status_schedules', 'Booked')->count();
        if ($activeSchedules > 0) {
            return back()->with('error', 'Tidak dapat menghapus lapangan yang memiliki jadwal aktif.');
        }

        $field->delete();
        return redirect()->route('admin.fields')->with('success', 'Lapangan berhasil dihapus.');
    }

    // ==================== USERS ====================

    public function users()
    {
        $users = User::query()->latest()->get();
        return view('admin.users', [
            'users' => $users,
            'totalUsers' => $users->count(),
            'activeNow' => User::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'banned' => User::query()->whereNotNull('banned_at')->count(),
        ]);
    }

    public function editUser($id)
    {
        $user = User::query()->findOrFail($id);
        return view('admin.user-edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::query()->findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id . ',id_users',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',id_users',
            'role' => 'required|in:admin,user,guest',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleBanUser($id)
    {
        $user = User::query()->findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat mem-ban admin.');
        }

        if ($user->banned_at) {
            $user->update(['banned_at' => null]);
            return redirect()->route('admin.users')->with('success', "Pengguna {$user->username} berhasil di-unban.");
        } else {
            $user->update(['banned_at' => now()]);
            return redirect()->route('admin.users')->with('success', "Pengguna {$user->username} berhasil di-ban.");
        }
    }

    // ==================== BOOKINGS ====================

    public function bookings()
    {
        $bookings = Booking::query()->with(['user', 'schedule.field', 'payment'])->latest()->get();
        return view('admin.bookings', compact('bookings'));
    }

    public function bookingDetail($id)
    {
        $booking = Booking::with([
            'schedule.field',
            'payment',
            'user'
        ])->findOrFail($id);

        return view('admin.booking-detail', compact('booking'));
    }

    public function bookingInvoice($id)
    {
        $booking = Booking::query()
            ->with(['schedule.field', 'payment', 'user'])
            ->findOrFail($id);

        return view('user.invoice', compact('booking'));
    }

    public function forcePaid($id)
    {
        $booking = Booking::query()->findOrFail($id);

        if ($booking->status_bookings === 'Paid') {
            return back()->with('info', 'Booking ini sudah berstatus Paid.');
        }

        $booking->update([
            'status_bookings' => 'Paid'
        ]);

        Payment::where('booking_id', $id)->update([
            'status_payments' => 'settlement',
            'paid_at' => now()
        ]);

        return redirect()->route('admin.bookings')->with('success', "Booking {$booking->booking_code} berhasil dipaksa menjadi Paid.");
    }

    public function cancelBooking($id)
    {
        $booking = Booking::query()->with('schedule.field')->findOrFail($id);

        if ($booking->status_bookings === 'Cancelled') {
            return back()->with('error', 'Booking ini sudah dibatalkan.');
        }

        $booking->update([
            'status_bookings' => 'Cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => 'Dibatalkan oleh admin',
        ]);

        // Free schedules
        $this->freeSchedules($booking);

        return redirect()->route('admin.bookings')->with('success', "Booking {$booking->booking_code} berhasil dibatalkan.");
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
        if (!$schedule || !$schedule->field) return;

        $pricePerHour = $schedule->field->price_per_hour;
        $duration = ($pricePerHour <= 0) ? 1 : round($booking->total_price / $pricePerHour);

        $schedulesToFree = Schedule::query()
            ->where('field_id', $schedule->field_id)
            ->where('date', $booking->play_date)
            ->where('start_time', '>=', $schedule->start_time)
            ->orderBy('start_time')
            ->limit($duration)
            ->pluck('id_schedules');

        if ($schedulesToFree->isEmpty()) return;

        Schedule::query()
            ->whereIn('id_schedules', $schedulesToFree)
            ->update(['status_schedules' => 'Available']);
    }
}
