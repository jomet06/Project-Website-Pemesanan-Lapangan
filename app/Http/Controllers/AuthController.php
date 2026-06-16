<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginSubmit(Request $request)
    {
        // Prototype: Cari user berdasarkan email yang diketik,
        // Jika tidak ketemu, login otomatis pakai User pertama (UserSeeder)
        $user = User::query()->where('email', $request->email)->first() ?? User::query()->first();

        if ($user) {
            Auth::login($user); // Memasukkan sesi pengguna ke sistem Laravel
            
            // Redirect sesuai role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil masuk sebagai Admin: ' . $user->name_users);
            }
            
            return redirect()->route('home')->with('success', 'Selamat datang kembali, ' . $user->name_users . '!');
        }

        return redirect()->route('home')->with('error', 'Gagal login. Belum ada data user di database (Jalankan seeder).');
    }

    public function registerSubmit()
    {
        return redirect()->route('login')->with('success', 'Pendaftaran prototipe berhasil! Silakan login.');
    }

    public function forgotPassword()
    {
        return redirect()->route('login')->with('success', 'Link reset password telah dikirim ke email prototipe Anda.');
    }

    public function googleAuth()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();

        $user = User::firstOrCreate(
            [
                'email' => $googleUser->email
            ],
            [
                'username' => explode('@', $googleUser->email)[0],
                'name_users' => $googleUser->name,
                'role' => 'user',
                'password' => bcrypt(Str::random(16))
            ]
        );

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Login Google berhasil');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}