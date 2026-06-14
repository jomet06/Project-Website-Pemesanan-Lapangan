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

    // Relationships
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

    // Business Logic
    public function canBeCancelled(): bool
    {
        if ($this->status_bookings === 'cancelled' || $this->status_bookings === 'completed') {
            return false;
        }
        // Can only cancel H-3 (3 days before play date)
        return Carbon::now()->addDays(3)->lte(Carbon::parse($this->play_date));
    }

    // Booking code generator
    public static function generateCode(): string
    {
        return 'BK-' . strtoupper(uniqid());
    }
}