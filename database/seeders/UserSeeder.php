<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name_users' => 'Admin ActiveCourt',
            'username'   => 'admin_ac',
            'email'      => 'admin@activecourt.id',
            'password'   => Hash::make('password123'),
            'role'       => 'admin',
        ]);

        User::create([
            'name_users' => 'Anthony Sinisukagunung',
            'username'   => 'anthony_s',
            'email'      => 'anthony@gmail.com',
            'password'   => Hash::make('password123'),
            'role'       => 'user',
        ]);

        User::create([
            'name_users' => 'Budi Santoso',
            'username'   => 'budi_s',
            'email'      => 'budi@gmail.com',
            'password'   => Hash::make('password123'),
            'role'       => 'user',
        ]);

        User::create([
            'name_users' => 'Citra Dewi',
            'username'   => 'citra_d',
            'email'      => 'citra@gmail.com',
            'password'   => Hash::make('password123'),
            'role'       => 'user',
        ]);
    }
}
