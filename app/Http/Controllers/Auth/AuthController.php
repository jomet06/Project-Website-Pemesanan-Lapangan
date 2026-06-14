<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // =====================
    // SHOW FORMS
    // =====================

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // =====================
    // LOGIN
    // =====================

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->intended(route('user.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // =====================
    // REGISTER
    // =====================

    public function register(Request $request)
    {
        $request->validate([
            'name_users' => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:users',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name_users' => $request->name_users,
            'username'   => $request->username,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('user.dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name_users);
    }

    // =====================
    // LOGOUT
    // =====================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Berhasil logout.');
    }

    // =====================
    // GOOGLE OAUTH
    // =====================

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                // Update google_id if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            } else {
                // Create new user from Google
                $username = explode('@', $googleUser->email)[0];
                $baseUsername = $username;
                $i = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $i;
                    $i++;
                }

                $user = User::create([
                    'name_users' => $googleUser->name,
                    'username'   => $username,
                    'email'      => $googleUser->email,
                    'google_id'  => $googleUser->id,
                    'password'   => null,
                    'role'       => 'user',
                ]);
            }

            Auth::login($user, true);

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard')->with('success', 'Login dengan Google berhasil!');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['google' => 'Login dengan Google gagal. Silahkan coba lagi.']);
        }
    }
}