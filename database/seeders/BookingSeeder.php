<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        if ($users->isEmpty()) return;

        $bookingData = [
            ['status' => 'confirmed', 'payStatus' => 'success', 'daysAhead' => 5],
            ['status' => 'confirmed', 'payStatus' => 'success', 'daysAhead' => 7],
            ['status' => 'pending',   'payStatus' => 'pending', 'daysAhead' => 10],
            ['status' => 'cancelled', 'payStatus' => 'cancel',  'daysAhead' => 3],
        ];

        foreach ($bookingData as $i => $data) {
            $user = $users[$i % $users->count()];

            // Find an available schedule
            $schedule = Schedule::where('status_schedules', 'available')
                ->where('date', Carbon::today()->addDays($data['daysAhead'])->format('Y-m-d'))
                ->first();

            if (!$schedule) continue;

            $field      = $schedule->field;
            $duration   = $schedule->duration_hours;
            $totalPrice = $duration * $field->price_per_hour;
            $code       = 'BK-' . strtoupper(substr(md5(uniqid()), 0, 8));

            $booking = Booking::create([
                'user_id'         => $user->id_users,
                'schedule_id'     => $schedule->id_schedules,
                'booking_code'    => $code,
                'total_price'     => $totalPrice,
                'status_bookings' => $data['status'],
                'play_date'       => $schedule->date,
                'cancelled_at'    => $data['status'] === 'cancelled' ? now() : null,
                'cancel_reason'   => $data['status'] === 'cancelled' ? 'Ada keperluan mendadak keluarga.' : null,
            ]);

            $schedule->update(['status_schedules' => $data['status'] === 'cancelled' ? 'available' : 'booked']);

            $orderId = 'ORDER-' . $booking->id_bookings . '-' . time() . $i;

            Payment::create([
                'booking_id'               => $booking->id_bookings,
                'midtrans_order_id'        => $orderId,
                'midtrans_transaction_id'  => $data['payStatus'] === 'success' ? 'TXN-' . strtoupper(uniqid()) : null,
                'amount'                   => $totalPrice,
                'payment_method'           => $data['payStatus'] === 'success' ? 'bank_transfer' : null,
                'status_payments'          => $data['payStatus'],
                'paid_at'                  => $data['payStatus'] === 'success' ? now()->subHours(rand(1, 24)) : null,
                'snap_token'               => $data['payStatus'] === 'pending' ? 'dummy-snap-token-' . uniqid() : null,
            ]);
        }
    }
}
