<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'SportBooking - Sewa Lapangan Olahraga')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { emerald: {"50":"#ecfdf5","100":"#d1fae5","200":"#a7f3d0","300":"#6ee7b7","400":"#34d399","500":"#10b981","600":"#059669","700":"#047857","800":"#065f46","900":"#064e3b"} }
                }
            }
        }
    </script>
    
    <!-- Alpine.js untuk interaksi UI (Dropdown, Mobile Menu) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-2xl font-bold text-emerald-600 flex items-center gap-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                        SportBooking
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="/" class="text-slate-600 hover:text-emerald-600 font-medium">Beranda</a>
                    <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium">Cari Lapangan</a>
                    <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium">Fasilitas</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                        <a href="#" class="text-slate-600 hover:text-emerald-600 font-medium">Masuk</a>
                        <a href="#" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg font-semibold transition">Daftar</a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-slate-700 hover:text-emerald-600 font-medium focus:outline-none">
                                <span>Halo, {{ Auth::user()->name_users }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-lg shadow-lg py-1 z-50" style="display: none;">
                                @if(Auth::user()->role === 'admin')
                                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Dashboard Admin</a>
                                @else
                                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Riwayat Booking</a>
                                @endif
                                <hr class="my-1 border-slate-200">
                                <form method="POST" action="#">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endGuest
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 hover:text-emerald-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" class="md:hidden bg-white border-t border-slate-200" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="/" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-md">Beranda</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-md">Cari Lapangan</a>
                @guest
                    <a href="#" class="block px-3 py-2 text-base font-medium text-emerald-600 hover:bg-emerald-50 rounded-md">Masuk</a>
                @else
                    <form method="POST" action="#">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-md">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-bold text-white mb-4">SportBooking</h3>
                <p class="text-sm">Platform pemesanan lapangan olahraga terpercaya, mudah, dan cepat. Temukan lapangan terbaik untuk pertandinganmu hari ini.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Tautan Cepat</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-emerald-400">Beranda</a></li>
                    <li><a href="#" class="hover:text-emerald-400">Daftar Lapangan</a></li>
                    <li><a href="#" class="hover:text-emerald-400">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Hubungi Kami</h3>
                <p class="text-sm">Email: support@sportbooking.com</p>
                <p class="text-sm">Telepon: +62 812 3456 7890</p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-8 border-t border-slate-700 text-center text-sm">
            &copy; {{ date('Y') }} Kelompok 10 - Sistem Pemesanan Lapangan Olahraga. All rights reserved.
        </div>
    </footer>

</body>
</html>