<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_users';

    // Kolom yang diizinkan untuk pengisian massal
    protected $fillable = [
        'username',
        'email',
        'password',
        'google_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Accessor for backward compatibility with views using name_users
    public function getNameUsersAttribute()
    {
        return $this->username;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'id_users');
    }
}
