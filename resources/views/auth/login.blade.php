<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - ActiveCourt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-panel {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #0f172a 100%);
        }
        .input-field {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: .625rem;
            padding: .65rem 1rem .65rem 2.75rem;
            font-size: .875rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .input-field:focus { border-color: #1a56db; box-shadow: 0 0 0 3px rgba(26,86,219,.12); }
        .input-field.error { border-color: #ef4444; }
        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: .6rem;
            width: 100%; padding: .7rem 1rem; border: 1.5px solid #e5e7eb;
            border-radius: .625rem; font-size: .875rem; font-weight: 600;
            color: #374151; background: white; cursor: pointer; transition: all .2s;
        }
        .btn-google:hover { background: #f9fafb; border-color: #d1d5db; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .btn-submit {
            width: 100%; padding: .75rem 1rem; background: #1a56db; color: white;
            border-radius: .625rem; font-size: .875rem; font-weight: 600;
            border: none; cursor: pointer; transition: background .2s;
        }
        .btn-submit:hover { background: #1e429f; }
        .divider { display: flex; align-items: center; gap: 1rem; }
        .divider::before, .divider::after { content:''; flex:1; height:1px; background:#e5e7eb; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex">

<!-- Left Panel — Branding -->
<div class="hidden lg:flex lg:w-1/2 hero-panel flex-col items-center justify-center p-12 relative overflow-hidden">
    <!-- Decorative circles -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500 opacity-5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400 opacity-5 rounded-full translate-y-1/3 -translate-x-1/4"></div>

    <div class="relative z-10 text-center max-w-sm">
        <!-- Logo -->
        <div class="flex items-center justify-center space-x-3 mb-12">
            <div class="w-12 h-12 bg-blue-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-basketball text-white text-xl"></i>
            </div>
            <span class="text-white font-bold text-3xl">ActiveCourt</span>
        </div>

        <h2 class="text-white text-4xl font-bold leading-tight mb-4">
            Secure Your Game.<br>Own The Court.
        </h2>
        <p class="text-blue-300 text-base mb-10 leading-relaxed">
            Platform profesional untuk mencari dan memesan lapangan olahraga premium dengan ketersediaan real-time.
        </p>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4">
            @foreach([['120+','Lapangan'], ['5000+','Member'], ['3','Olahraga']] as $s)
            <div class="bg-white bg-opacity-10 rounded-xl p-4 text-center">
                <p class="text-white text-2xl font-bold">{{ $s[0] }}</p>
                <p class="text-blue-300 text-xs mt-1">{{ $s[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Right Panel — Form -->
<div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
    <div class="w-full max-w-md">
        <!-- Mobile logo -->
        <div class="flex lg:hidden items-center space-x-2 mb-8 justify-center">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-basketball text-white"></i>
            </div>
            <span class="text-gray-900 font-bold text-2xl">ActiveCourt</span>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-1">Selamat datang kembali!</h1>
        <p class="text-gray-500 text-sm mb-8">Masuk untuk melanjutkan ke akun Anda</p>

        <!-- Error alert -->
        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-start gap-2">
                <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Google OAuth Button -->
        <a href="{{ route('auth.google') }}" class="btn-google mb-5">
            <svg width="20" height="20" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Lanjutkan dengan Google
        </a>

        <!-- Divider -->
        <div class="divider text-gray-400 text-xs mb-5">atau masuk dengan email</div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="nama@email.com"
                           class="input-field {{ $errors->has('email') ? 'error' : '' }}">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="fas fa-circle-exclamation"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" name="password" id="passwordInput" required
                           placeholder="Masukkan password"
                           class="input-field pr-10 {{ $errors->has('password') ? 'error' : '' }}">
                    <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="eyeIcon" class="fas fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-primary rounded border-gray-300">
                    Ingat saya
                </label>
                <a href="#" class="text-sm text-primary hover:text-blue-800 font-medium">Lupa password?</a>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit">
                <i class="fas fa-sign-in-alt mr-2"></i>Masuk Sekarang
            </button>
        </form>

        <!-- Register link -->
        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary font-semibold hover:text-blue-800">Daftar gratis</a>
        </p>

        <!-- Back to home -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke beranda
            </a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }
</script>
</body>
</html>