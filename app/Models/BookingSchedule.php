<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSchedule extends Model
{
    public $timestamps = false;

    protected $fillable = ['booking_id', 'schedule_id'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id_schedules');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id_bookings');
    }
}
