<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - ActiveCourt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1a56db', dark: '#1e429f', light: '#3f83f8' },
                        sidebar: '#1e2432',
                        sidebarbg: '#161c2d',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link { transition: all .15s; border-radius: .5rem; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(59,130,246,.15); color: #60a5fa; }
        .sidebar-link.active { border-left: 3px solid #1a56db; }
        .stat-card { background: white; border-radius: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

<!-- SIDEBAR -->
<aside class="w-60 bg-sidebarbg text-white flex flex-col flex-shrink-0 h-full overflow-y-auto">
    <!-- Logo -->
    <div class="px-5 py-5 border-b border-gray-700">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                <i class="fas fa-basketball text-white text-sm"></i>
            </div>
            <div>
                <p class="text-white font-bold text-base leading-none">Admin Portal</p>
                <p class="text-gray-400 text-xs">Management Console</p>
            </div>
        </a>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 space-y-1">
        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-2 mb-2">Main Menu</p>
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link flex items-center space-x-3 px-3 py-2.5 text-gray-300 text-sm {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large w-5 text-center"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('admin.fields.index') }}"
           class="sidebar-link flex items-center space-x-3 px-3 py-2.5 text-gray-300 text-sm {{ request()->routeIs('admin.fields*') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt w-5 text-center"></i><span>Field Management</span>
        </a>
        <a href="{{ route('admin.schedules.index') }}"
           class="sidebar-link flex items-center space-x-3 px-3 py-2.5 text-gray-300 text-sm {{ request()->routeIs('admin.schedules*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt w-5 text-center"></i><span>Schedule Management</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link flex items-center space-x-3 px-3 py-2.5 text-gray-300 text-sm {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="fas fa-users w-5 text-center"></i><span>User Management</span>
        </a>
        <a href="{{ route('admin.bookings.index') }}"
           class="sidebar-link flex items-center space-x-3 px-3 py-2.5 text-gray-300 text-sm {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list w-5 text-center"></i><span>Bookings</span>
        </a>
    </nav>

    <!-- User Info -->
    <div class="border-t border-gray-700 px-3 py-4">
        <div class="flex items-center space-x-3 px-2">
            <div class="w-9 h-9 rounded-full bg-primary-dark flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name_users, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name_users }}</p>
                <p class="text-gray-400 text-xs">Super Admin</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="w-full text-left sidebar-link flex items-center space-x-3 px-3 py-2 text-gray-400 text-sm hover:text-red-400">
                <i class="fas fa-sign-out-alt w-5 text-center"></i><span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<div class="flex-1 flex flex-col overflow-hidden">
    <!-- Top bar -->
    <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
        <div>
            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <p class="text-xs text-gray-500">@yield('page-subtitle', '')</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(session('success'))
                <span class="text-green-600 text-sm flex items-center"><i class="fas fa-check-circle mr-1"></i>{{ session('success') }}</span>
            @endif
            <span class="text-gray-400 text-sm">{{ now()->format('d M Y') }}</span>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto p-6">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center justify-between">
                <span class="flex items-center"><i class="fas fa-check-circle mr-2 text-green-500"></i>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"><i class="fas fa-times text-green-600"></i></button>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3">
                <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>{{ $errors->first() }}
            </div>
        @endif
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>