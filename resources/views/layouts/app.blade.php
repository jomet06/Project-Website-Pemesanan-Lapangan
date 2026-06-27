<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'ActiveCourt - Sewa Lapangan Olahraga')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa',
                            500: '#3b82f6', 600: '#1d4ed8', 700: '#1e3a5f', 800: '#1e2d4f', 900: '#0f1b33',
                        },
                        accent: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74', 400: '#fb923c',
                            500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased flex flex-col min-h-screen">

    <div class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[100] flex flex-col gap-3 w-full max-w-md px-4 pointer-events-none">
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white border-l-4 border-green-500 shadow-2xl rounded-xl p-4 flex items-start gap-3 pointer-events-auto">
            <div class="bg-green-100 rounded-full p-1.5 flex-shrink-0 mt-0.5">
                <i class="fas fa-check text-green-600 text-sm"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-slate-800">Berhasil!</h4>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition"><i class="fas fa-times"></i></button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white border-l-4 border-red-500 shadow-2xl rounded-xl p-4 flex items-start gap-3 pointer-events-auto">
            <div class="bg-red-100 rounded-full p-1.5 flex-shrink-0 mt-0.5">
                <i class="fas fa-exclamation text-red-600 text-sm px-1"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-slate-800">Oops! Terjadi Kesalahan</h4>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('error') }}</p>
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition"><i class="fas fa-times"></i></button>
        </div>
        @endif

        @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white border-l-4 border-blue-500 shadow-2xl rounded-xl p-4 flex items-start gap-3 pointer-events-auto">
            <div class="bg-blue-100 rounded-full p-1.5 flex-shrink-0 mt-0.5">
                <i class="fas fa-info-circle text-blue-600 text-sm"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-slate-800">Info</h4>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('info') }}</p>
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition"><i class="fas fa-times"></i></button>
        </div>
        @endif
    </div>

    @if(session()->has('reschedule_booking_id'))
    <div class="bg-blue-600 text-white px-4 py-2 flex items-center justify-center gap-4 text-sm font-medium z-50">
        <span>Anda sedang dalam mode Reschedule. Silakan pilih jadwal pengganti.</span>
        <form action="{{ route('booking.cancel-reschedule') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-white text-xs font-bold transition">Batal Reschedule</button>
        </form>
    </div>
    @endif

    @if(!request()->routeIs('login'))
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-extrabold text-primary-700 flex items-center gap-2">
                        <i class="fas fa-layer-group text-primary-600 text-2xl"></i> ActiveCourt
                    </a>
                </div>

                <div class="hidden md:flex space-x-6 items-center h-full">
                    <a href="{{ route('home') }}" class="text-sm font-bold {{ request()->routeIs('home') ? 'text-primary-600 border-b-2 border-primary-600' : 'text-slate-500 hover:text-slate-800' }} h-full flex items-center px-1 transition-colors">Home</a>
                    <a href="{{ route('fields.index') }}" class="text-sm font-bold {{ request()->routeIs('fields.*') ? 'text-primary-600 border-b-2 border-primary-600' : 'text-slate-500 hover:text-slate-800' }} h-full flex items-center px-1 transition-colors">Fields</a>
                    @auth
                        <a href="{{ route('user.history') }}" class="text-sm font-bold {{ request()->routeIs('user.history') ? 'text-primary-600 border-b-2 border-primary-600' : 'text-slate-500 hover:text-slate-800' }} h-full flex items-center px-1 transition-colors">History</a>
                    @endauth
                    <a href="{{ route('about') }}" class="text-sm font-bold {{ request()->routeIs('about') ? 'text-primary-600 border-b-2 border-primary-600' : 'text-slate-500 hover:text-slate-800' }} h-full flex items-center px-1 transition-colors">About</a>
                    <a href="{{ route('contact') }}" class="text-sm font-bold {{ request()->routeIs('contact') ? 'text-primary-600 border-b-2 border-primary-600' : 'text-slate-500 hover:text-slate-800' }} h-full flex items-center px-1 transition-colors">Contact</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" placeholder="Search fields..." class="bg-slate-100 border border-transparent text-sm rounded-full pl-9 pr-4 py-1.5 focus:bg-white focus:border-slate-300 focus:ring-2 focus:ring-primary-500 w-48 transition-all outline-none">
                    </div>
                    
                    @guest
                        <a href="{{ route('login') }}" class="text-slate-600 hover:text-primary-600 font-bold text-sm px-2 transition">Sign In</a>
                        <a href="{{ route('register') }}" class="bg-primary-600 text-white hover:bg-primary-700 px-5 py-2 rounded-full text-sm font-bold transition shadow-md hover:shadow-lg">Sign Up</a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 focus:outline-none pl-2 border-l border-slate-200">
                                <div class="text-right hidden lg:block">
                                    <p class="text-sm font-bold text-slate-800 leading-tight">{{ Auth::user()->username }}</p>
                                    <p class="text-xs text-slate-500 font-medium capitalize">{{ Auth::user()->role }}</p>
                                </div>
                                <div class="w-9 h-9 rounded-full border-2 border-primary-100 overflow-hidden bg-slate-200">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->username) }}&background=1d4ed8&color=fff&bold=true" class="w-full h-full object-cover">
                                </div>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50" style="display: none;">

                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-primary-50 hover:text-primary-600 transition">
                                        <i class="fas fa-tachometer-alt w-5 text-center"></i> Dashboard Admin
                                    </a>
                                @endif
                                <a href="{{ route('user.history') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-primary-50 hover:text-primary-600 transition">
                                    <i class="fas fa-history w-5 text-center"></i> Riwayat Booking
                                </a>

                                <div class="h-px bg-slate-100 my-2"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-sign-out-alt w-5 text-center"></i> Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
    @endif

    <main class="flex-grow">
        @yield('content')
    </main>

    @if(!request()->routeIs('login'))
    <footer class="bg-primary-900 text-slate-300 pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-layer-group text-primary-500"></i> ActiveCourt
                    </h3>
                    <p class="text-sm text-slate-400 max-w-md">Platform pemesanan lapangan olahraga terpercaya, mudah, dan cepat. Temukan lapangan terbaik untuk pertandinganmu hari ini.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2 text-sm font-medium">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="{{ route('fields.index') }}" class="hover:text-white transition">Daftar Lapangan</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Kontak</a></li>
                        <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Hubungi Kami</h3>
                    <ul class="space-y-3 text-sm font-medium">
                        <li class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center"><i class="fas fa-envelope text-white"></i></div> support@activecourt.com</li>
                        <li class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center"><i class="fas fa-phone text-white"></i></div> +62 812 3456 7890</li>
                    </ul>
                </div>
            </div>
            <div class="pt-6 border-t border-white/10 text-center text-sm font-medium text-slate-500">
                &copy; {{ date('Y') }} Kelompok 10 - ActiveCourt. All rights reserved.
            </div>
        </div>
    </footer>
    @endif

    @stack('scripts')
</body>
</html>
