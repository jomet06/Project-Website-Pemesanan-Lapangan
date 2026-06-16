<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_schedules';

    protected $fillable = [
        'field_id',
        'court_number',
        'date',
        'start_time',
        'end_time',
        'status_schedules',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id', 'id_fields');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'schedule_id', 'id_schedules');
    }

    public function scopeAvailable(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('status_schedules', 'available');
    }

    public function getDurationHoursAttribute(): float
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end   = \Carbon\Carbon::parse($this->end_time);
        return $start->diffInMinutes($end) / 60;
    }
}
