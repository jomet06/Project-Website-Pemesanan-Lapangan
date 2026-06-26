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
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil masuk sebagai Admin: ' . $user->username);
            }
            
            return redirect()->route('home')->with('success', 'Selamat datang kembali, ' . $user->username . '!');
        }

        return redirect()->route('home')->with('error', 'Gagal login. Belum ada data user di database (Jalankan seeder).');
    }

    public function registerSubmit(Request $request)
    {
        $request->validate([
            'name_users' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name_users' => $request->name_users,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login.');
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