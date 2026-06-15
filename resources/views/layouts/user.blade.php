<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - ActiveCourt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: { DEFAULT:'#1a56db', dark:'#1e429f' }, accent:'#f97316' } } } }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>body { font-family:'Inter',sans-serif; } .nav-active { color:#1a56db; border-bottom:2px solid #1a56db; }</style>
    @stack('styles')
</head>
<body class="bg-gray-50">

<!-- NAVBAR -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                    <i class="fas fa-basketball text-white text-sm"></i>
                </div>
                <span class="text-gray-900 font-bold text-xl">ActiveCourt</span>
            </a>
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('home') }}" class="px-4 py-5 text-sm font-medium text-gray-600 hover:text-primary">Home</a>
                <a href="{{ route('user.fields.search') }}" class="px-4 py-5 text-sm font-medium {{ request()->routeIs('user.fields*') ? 'nav-active text-primary' : 'text-gray-600 hover:text-primary' }}">Fields</a>
                <a href="{{ route('user.bookings.history') }}" class="px-4 py-5 text-sm font-medium {{ request()->routeIs('user.bookings.history') ? 'nav-active text-primary' : 'text-gray-600 hover:text-primary' }}">History</a>
                <a href="{{ route('guest.about') }}" class="px-4 py-5 text-sm font-medium text-gray-600 hover:text-primary">About</a>
                <a href="{{ route('guest.contact') }}" class="px-4 py-5 text-sm font-medium text-gray-600 hover:text-primary">Contact</a>
            </div>
            <div class="flex items-center space-x-3">
                <form action="{{ route('user.fields.search') }}" method="GET" class="hidden md:block">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" placeholder="Search fields..." value="{{ request('search') }}"
                               class="bg-gray-100 pl-9 pr-4 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary w-44">
                    </div>
                </form>
                <!-- User dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button onclick="document.getElementById('userDrop').classList.toggle('hidden')"
                            class="flex items-center space-x-2 bg-gray-100 rounded-full pl-3 pr-1 py-1 hover:bg-gray-200">
                        <span class="text-sm font-semibold text-gray-700">{{ auth()->user()->name_users }}</span>
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->name_users, 0, 1)) }}
                        </div>
                    </button>
                    <div id="userDrop" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                        <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><i class="fas fa-th-large mr-3 text-gray-400"></i>Dashboard</a>
                        <a href="{{ route('user.bookings.history') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><i class="fas fa-history mr-3 text-gray-400"></i>Riwayat Booking</a>
                        <hr class="my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt mr-3"></i>Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Flash -->
@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center justify-between">
            <span><i class="fas fa-check-circle mr-2 text-green-500"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times text-green-500"></i></button>
        </div>
    </div>
@endif
@if($errors->any())
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
        </div>
    </div>
@endif

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>

<footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500">
        <span class="font-semibold text-gray-700">ActiveCourt</span>
        <div class="flex space-x-5 mt-3 md:mt-0">
            <a href="#" class="hover:text-gray-700">Terms of Service</a>
            <a href="#" class="hover:text-gray-700">Privacy Policy</a>
            <a href="#" class="hover:text-gray-700">Project Info</a>
            <a href="#" class="hover:text-gray-700">Support</a>
        </div>
    </div>
</footer>

<script>
    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
        const drop = document.getElementById('userDrop');
        if (drop && !drop.previousElementSibling.contains(e.target)) drop.classList.add('hidden');
    });
</script>
@stack('scripts')
</body>
</html>