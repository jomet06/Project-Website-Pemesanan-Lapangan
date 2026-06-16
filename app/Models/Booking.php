<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_bookings';

    protected $fillable = [
        'user_id',
        'schedule_id',
        'booking_code',
        'total_price',
        'status_bookings',
        'cancelled_at',
        'cancel_reason',
        'play_date',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'play_date'    => 'date',
        'total_price'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_users');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id_schedules');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'id_bookings');
    }

    public function bookingSchedules()
    {
        return $this->hasMany(BookingSchedule::class, 'booking_id', 'id_bookings');
    }

    public function canBeCancelled(): bool
    {
        if ($this->status_bookings === 'cancelled' || $this->status_bookings === 'completed') {
            return false;
        }
        return Carbon::now()->addDays(3)->lte(Carbon::parse($this->play_date));
    }

    public static function generateCode(): string
    {
        return 'BK-' . strtoupper(uniqid());
    }

    /**
     * Free all schedule slots linked to this booking.
     */
    public function freeAllSchedules(): void
    {
        $ids = $this->bookingSchedules->pluck('schedule_id')->toArray();
        if ($this->schedule_id && !in_array($this->schedule_id, $ids)) {
            $ids[] = $this->schedule_id;
        }
        if (!empty($ids)) {
            Schedule::whereIn('id_schedules', $ids)->update(['status_schedules' => 'available']);
        }
    }
}
