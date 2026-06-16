<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ActiveCourt - Booking Lapangan Olahraga')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:  { DEFAULT: '#1a56db', dark: '#1e429f', light: '#3f83f8' },
                        dark:     { DEFAULT: '#111827', card: '#1f2937', nav: '#0f172a' },
                        accent:   '#f97316',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
        }
        .card-hover { transition: transform .2s, box-shadow .2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.25); }
        .nav-link { transition: color .15s; }
        .badge-available { background: #d1fae5; color: #065f46; }
        .badge-booked    { background: #fee2e2; color: #991b1b; }
        .badge-pending   { background: #fef3c7; color: #92400e; }
        .btn-primary {
            background: #1a56db;
            color: white;
            padding: .5rem 1.25rem;
            border-radius: .5rem;
            font-weight: 600;
            transition: background .2s;
        }
        .btn-primary:hover { background: #1e429f; }
        .btn-accent {
            background: #f97316;
            color: white;
            padding: .5rem 1.25rem;
            border-radius: .5rem;
            font-weight: 600;
            transition: background .2s;
        }
        .btn-accent:hover { background: #ea6c05; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900">

<!-- NAVBAR -->
<nav class="bg-dark-nav shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                    <i class="fas fa-basketball text-white text-sm"></i>
                </div>
                <span class="text-white font-bold text-xl">ActiveCourt</span>
            </a>

            <!-- Nav Links -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="nav-link text-gray-300 hover:text-white text-sm font-medium {{ request()->routeIs('home') ? 'text-white' : '' }}">Home</a>
                <a href="{{ route('guest.fields') }}" class="nav-link text-gray-300 hover:text-white text-sm font-medium {{ request()->routeIs('guest.fields*') ? 'text-white' : '' }}">Fields</a>
                <a href="{{ route('guest.about') }}" class="nav-link text-gray-300 hover:text-white text-sm font-medium">About</a>
                <a href="{{ route('guest.contact') }}" class="nav-link text-gray-300 hover:text-white text-sm font-medium">Contact</a>
            </div>

            <!-- Search + Auth -->
            <div class="flex items-center space-x-3">
                <form action="{{ route('guest.fields') }}" method="GET" class="hidden md:block">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" placeholder="Search fields..."
                               class="bg-gray-700 text-white pl-9 pr-4 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary w-48"
                               value="{{ request('search') }}">
                    </div>
                </form>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn-primary text-sm">
                            <i class="fas fa-tachometer-alt mr-1"></i> Admin
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="btn-primary text-sm">
                            <i class="fas fa-th-large mr-1"></i> Dashboard
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="text-gray-300 hover:text-white text-sm font-medium">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm font-medium">Sign In</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm">Get Started</a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <button id="mobileMenuBtn" class="md:hidden text-gray-300 hover:text-white">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-dark-nav border-t border-gray-700 px-4 py-3 space-y-2">
        <a href="{{ route('home') }}" class="block text-gray-300 hover:text-white py-2">Home</a>
        <a href="{{ route('guest.fields') }}" class="block text-gray-300 hover:text-white py-2">Fields</a>
        <a href="{{ route('guest.about') }}" class="block text-gray-300 hover:text-white py-2">About</a>
        @auth
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="block text-white font-semibold py-2">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="block text-gray-300 hover:text-white py-2">Sign In</a>
            <a href="{{ route('register') }}" class="block text-white font-semibold py-2">Register</a>
        @endauth
    </div>
</nav>

<!-- Flash Messages -->
@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-3 flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2 text-green-500"></i>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error') || $errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-3">
        <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
        {{ session('error') ?? $errors->first() }}
    </div>
@endif

<!-- CONTENT -->
<main>
    @yield('content')
</main>

<!-- FOOTER -->
<footer class="bg-dark-nav text-gray-400 mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <div class="w-7 h-7 bg-primary rounded-lg flex items-center justify-center">
                    <i class="fas fa-basketball text-white text-xs"></i>
                </div>
                <span class="text-white font-bold">ActiveCourt</span>
            </div>
            <div class="flex space-x-6 text-sm">
                <a href="{{ route('guest.about') }}" class="hover:text-white">Tentang Kami</a>
                <a href="{{ route('guest.contact') }}" class="hover:text-white">Kontak</a>
                <a href="{{ route('guest.fields') }}" class="hover:text-white">Lapangan</a>
            </div>
        </div>
        <p class="text-center text-xs mt-4 text-gray-600">© {{ date('Y') }} ActiveCourt. All rights reserved.</p>
    </div>
</footer>

<script>
    // Mobile menu toggle
    document.getElementById('mobileMenuBtn')?.addEventListener('click', () => {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });
    // Auto-dismiss flash
    setTimeout(() => document.querySelectorAll('[data-flash]').forEach(el => el.remove()), 5000);
</script>
@stack('scripts')
</body>
</html>