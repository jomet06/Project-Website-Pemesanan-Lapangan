<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $paymentType = $request->payment_type;
        $transactionId = $request->transaction_id;
        $fraudStatus = $request->fraud_status;

        $payment = Payment::where('midtrans_order_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->midtrans_transaction_id = $transactionId;
        $payment->payment_method = $paymentType;

        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            if ($fraudStatus == 'accept' || $fraudStatus == null) {
                $payment->status_payments = 'settlement';
                $payment->paid_at = Carbon::now();

                $booking = $payment->booking;
                if ($booking) {
                    $booking->status_bookings = 'Paid';
                    $booking->save();
                }
            }
        } elseif ($transactionStatus == 'pending') {
            $payment->status_payments = 'pending';
        } elseif (
            $transactionStatus == 'expire' ||
            $transactionStatus == 'cancel' ||
            $transactionStatus == 'deny'
        ) {
            $payment->status_payments = 'failed';

            $booking = $payment->booking;
            if ($booking && $booking->status_bookings !== 'Cancelled') {
                $booking->status_bookings = 'Cancelled';
                $booking->cancelled_at = Carbon::now();
                $booking->save();

                // Free schedules
                $this->freeSchedules($booking);
            }
        }

        $payment->save();

        return response()->json(['success' => true]);
    }

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

    //Tambahan bisa dihapus nanti kalo sudah deploy
    public function forcePaid($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status_bookings' => 'Paid'
        ]);

        Payment::where('booking_id', $id)->update([
            'status_payments' => 'settlement',
            'paid_at' => now()
        ]);

        return response()->json([
            'success' => true
        ]);
    }

}
