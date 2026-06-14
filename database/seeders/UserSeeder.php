<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Akun Admin
        User::create([
            'name_users' => 'Admin Lapangan',
            'username'   => 'admin_sport',
            'email'      => 'admin@gmail.com',
            'password'   => Hash::make('password123'),
            'role'       => 'admin',
        ]);

        // Buat Akun User Contoh 1
        User::create([
            'name_users' => 'Aldo Kurniawan',
            'username'   => 'aldo_kt',
            'email'      => 'user@gmail.com',
            'password'   => Hash::make('password123'),
            'role'       => 'user',
        ]);

        // Buat Akun User Contoh 2
        User::create([
            'name_users' => 'Vinsens Sandri',
            'username'   => 'vinsens_s',
            'email'      => 'vinsens@gmail.com',
            'password'   => Hash::make('password123'),
            'role'       => 'user',
        ]);
    }
}