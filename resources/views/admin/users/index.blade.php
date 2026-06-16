@extends('layouts.admin')
@section('title','User Management')
@section('page-title','User Management')
@section('page-subtitle','Manage member accounts, adjust permissions, and monitor activity.')
@section('content')

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $totalUsers  = \App\Models\User::where('role','user')->count();
    $totalAdmins = \App\Models\User::where('role','admin')->count();
    @endphp
    @foreach([
        ['Total Users', $totalUsers, 'fas fa-users', 'bg-blue-50 text-blue-600'],
        ['Admins', $totalAdmins, 'fas fa-shield-alt', 'bg-red-50 text-red-600'],
    ] as $s)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl {{ explode(' ',$s[3])[0] }} flex items-center justify-center flex-shrink-0">
            <i class="{{ $s[2] }} {{ explode(' ',$s[3])[1] }} text-lg"></i>
        </div>
        <div>
            <p class="font-bold text-gray-900 text-xl leading-tight">{{ $s[1] }}</p>
            <p class="text-gray-400 text-xs">{{ $s[0] }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- Search & Filter -->
<div class="flex flex-wrap items-center gap-3 mb-5">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2 flex-1">
        <div class="relative flex-1 min-w-48">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, email, atau username..."
                   class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <select name="role" onchange="this.form.submit()"
                class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Semua Role</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="bg-primary hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded-xl text-sm transition">Cari</button>
        @if(request()->hasAny(['search','role']))
        <a href="{{ route('admin.users.index') }}" class="text-sm text-red-500 hover:text-red-700 px-3 py-2">Reset</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Join Date</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bookings</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($user->name_users, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm leading-none">{{ $user->name_users }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">@{{ $user->username }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                <td class="px-5 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                <td class="px-5 py-4 text-sm font-semibold text-gray-900">{{ $user->bookings_count }}</td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.users.show', $user) }}"
                           class="text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        @if($user->id_users !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Hapus user {{ $user->name_users }}? Semua data booking terkait akan ikut dihapus.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">
                <i class="fas fa-users text-4xl mb-3 opacity-30"></i>
                <p>Tidak ada user ditemukan</p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
<div class="mt-5 flex justify-center">{{ $users->appends(request()->query())->links() }}</div>
@endif
@endsection
