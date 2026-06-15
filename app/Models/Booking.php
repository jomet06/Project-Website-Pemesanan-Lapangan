<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'id_bookings';

    // Kolom yang diizinkan untuk pengisian massal
    protected $fillable = [
        'user_id',
        'schedule_id',
        'booking_code',
        'total_price',
        'status_bookings',
        'play_date',
        'cancelled_at',
        'cancel_reason',
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
}