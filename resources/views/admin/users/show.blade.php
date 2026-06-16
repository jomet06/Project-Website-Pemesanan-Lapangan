@extends('layouts.admin')
@section('title', $user->name_users)
@section('page-title', $user->name_users)
@section('page-subtitle', 'Detail akun dan riwayat booking pengguna.')
@section('content')

<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition">
        <i class="fas fa-arrow-left"></i>Kembali ke Daftar User
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Booking History -->
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Riwayat Booking</h3>
            </div>
            @forelse($user->bookings as $booking)
            @php
            $statusStyle = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700','completed'=>'bg-gray-100 text-gray-600'][$booking->status_bookings] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-futbol text-primary text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $booking->schedule->field->name_fields ?? '-' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            <span class="font-mono">{{ $booking->booking_code }}</span> &middot;
                            {{ $booking->play_date?->format('d M Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0 ml-3">
                    <span class="font-semibold text-sm text-gray-900">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $statusStyle }}">{{ ucfirst($booking->status_bookings) }}</span>
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="text-xs text-primary font-semibold hover:underline">Detail</a>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-400">
                <i class="fas fa-calendar-times text-3xl mb-2 opacity-30"></i>
                <p class="text-sm">Belum ada booking</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-5">
        <!-- User Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="w-20 h-20 rounded-full bg-primary flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                {{ strtoupper(substr($user->name_users, 0, 1)) }}
            </div>
            <h3 class="font-bold text-gray-900 text-lg">{{ $user->name_users }}</h3>
            <p class="text-gray-500 text-sm">@{{ $user->username }}</p>
            <p class="text-gray-400 text-xs mt-0.5">{{ $user->email }}</p>
            <div class="mt-3">
                <span class="text-xs font-bold px-3 py-1.5 rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-4">Bergabung {{ $user->created_at->format('d M Y') }}</p>
            @if($user->google_id)
            <p class="text-xs text-green-600 mt-1"><i class="fab fa-google mr-1"></i>Login via Google</p>
            @endif
        </div>

        <!-- Stats -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Statistik</h3>
            @php
            $totalB  = $user->bookings->count();
            $confB   = $user->bookings->where('status_bookings','confirmed')->count();
            $cancelB = $user->bookings->where('status_bookings','cancelled')->count();
            $totalS  = $user->bookings->where('payment.status_payments','success')->sum('total_price') ?? 0;
            @endphp
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Total Booking</span><span class="font-bold text-gray-900">{{ $totalB }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Dikonfirmasi</span><span class="font-bold text-green-600">{{ $confB }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Dibatalkan</span><span class="font-bold text-red-500">{{ $cancelB }}</span></div>
                <div class="flex justify-between border-t border-gray-100 pt-2 mt-2">
                    <span class="text-gray-500">Total Belanja</span>
                    <span class="font-bold text-primary">Rp {{ number_format($user->bookings->where('status_bookings','confirmed')->sum('total_price'),0,',','.') }}</span>
                </div>
            </div>
        </div>

        <!-- Change Role -->
        @if($user->id_users !== auth()->id())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Ubah Role</h3>
            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                @csrf @method('PATCH')
                <select name="role" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white mb-3">
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                <button type="submit" class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-2.5 rounded-xl text-sm transition">
                    Simpan Role
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
              onsubmit="return confirm('Hapus user ini secara permanen? Semua data booking terkait akan ikut dihapus.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 rounded-xl text-sm transition">
                <i class="fas fa-trash"></i>Hapus User
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
