<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin - ActiveCourt')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#1d4ed8',
                            700: '#1e3a5f',
                            800: '#1e2d4f',
                            900: '#0f1b33',
                            950: '#080f1f',
                        },
                        accent: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-primary-800 text-white flex flex-col transition-transform duration-300 lg:translate-x-0">
            
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-primary-700">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-xl font-bold">
                    <svg class="w-7 h-7 text-accent-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-5-5 1.41-1.41L11 14.17l7.59-7.59L20 8l-9 9z"/>
                    </svg>
                    ActiveCourt
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Admin Info -->
            <div class="px-6 py-4 border-b border-primary-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent-500 rounded-full flex items-center justify-center text-sm font-bold">A</div>
                    <div>
                        <p class="text-sm font-semibold text-white">Admin</p>
                        <p class="text-xs text-primary-300">{{ Auth::user()->email ?? 'admin@activecourt.com' }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-accent-500 text-white shadow-md' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.fields') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.fields*') ? 'bg-accent-500 text-white shadow-md' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                    <i class="fas fa-map-marked-alt w-5 text-center"></i>
                    Field Management
                </a>
                <a href="{{ route('admin.users') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.users*') ? 'bg-accent-500 text-white shadow-md' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    User Management
                </a>
                <a href="{{ route('admin.bookings') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.bookings*') ? 'bg-accent-500 text-white shadow-md' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                    <i class="fas fa-calendar-check w-5 text-center"></i>
                    Bookings
                </a>
            </nav>

            <!-- Bottom Actions -->
            <div class="px-3 py-4 border-t border-primary-700">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-primary-200 hover:bg-primary-700 hover:text-white transition">
                    <i class="fas fa-arrow-left w-5 text-center"></i>
                    Kembali ke Website
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-red-300 hover:bg-red-500/20 hover:text-red-200 transition mt-1">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 lg:px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 hidden lg:block">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-lg font-bold text-slate-800">@yield('page-title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-500 hidden sm:block">{{ now()->format('d M Y') }}</span>
                    <div class="w-8 h-8 bg-accent-500 rounded-full flex items-center justify-center text-white text-xs font-bold">A</div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
