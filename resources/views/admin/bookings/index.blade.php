@extends('layouts.admin')
@section('title','Bookings Management')
@section('page-title','Bookings Management')
@section('page-subtitle','Monitor, verify, and manage all field reservations in real-time.')
@section('content')

<!-- Stats Row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $allCount    = \App\Models\Booking::count();
    $pendCount   = \App\Models\Booking::where('status_bookings','pending')->count();
    $confCount   = \App\Models\Booking::where('status_bookings','confirmed')->count();
    $revenue     = \App\Models\Payment::where('status_payments','success')->sum('amount');
    @endphp
    @foreach([
        ['Total Bookings', $allCount, 'fas fa-clipboard-list', 'bg-blue-50 text-blue-600'],
        ['Pending Verification', $pendCount, 'fas fa-clock', 'bg-yellow-50 text-yellow-600'],
        ['Confirmed', $confCount, 'fas fa-check-circle', 'bg-green-50 text-green-600'],
        ['Revenue', 'Rp '.number_format($revenue,0,',','.'), 'fas fa-wallet', 'bg-purple-50 text-purple-600'],
    ] as $s)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl {{ explode(' ',$s[3])[0] }} flex items-center justify-center flex-shrink-0">
            <i class="{{ $s[2] }} {{ explode(' ',$s[3])[1] }} text-lg"></i>
        </div>
        <div>
            <p class="font-bold text-gray-900 text-base leading-tight">{{ $s[1] }}</p>
            <p class="text-gray-400 text-xs">{{ $s[0] }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- Search & Filter -->
<div class="flex flex-wrap items-center gap-3 mb-5">
    <a href="{{ route('admin.bookings.offline.create') }}"
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition shrink-0">
        <i class="fas fa-plus-circle"></i> Buat Booking Offline
    </a>
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex flex-wrap gap-2 flex-1">
        <div class="relative flex-1 min-w-48">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode booking, nama user, lapangan..."
                   class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <select name="status" onchange="this.form.submit()"
                class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Semua Status</option>
            @foreach(['pending','confirmed','cancelled','completed'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-primary hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded-xl text-sm transition">Filter</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-red-500 hover:text-red-700 px-3 py-2">Reset</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">Recent Transactions</h3>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Booking ID</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Field</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date/Time</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($bookings as $booking)
            @php
            $statusStyle = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700','completed'=>'bg-gray-100 text-gray-600'][$booking->status_bookings] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-4">
                    <span class="font-mono font-bold text-primary text-sm">#{{ $booking->booking_code }}</span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                            {{ strtoupper(substr($booking->user->name_users ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 leading-none">{{ $booking->user->name_users ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $booking->user->email ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4">
                    <p class="text-sm font-semibold text-gray-900">{{ $booking->schedule->field->name_fields ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $booking->schedule->field->type_fields ?? '' }}</p>
                </td>
                <td class="px-5 py-4">
                    <p class="text-sm text-gray-900 font-medium">{{ $booking->play_date?->format('d M Y') }}</p>
                    <p class="text-xs text-gray-400">{{ substr($booking->schedule->start_time ?? '',0,5) }} – {{ substr($booking->schedule->end_time ?? '',0,5) }}</p>
                </td>
                <td class="px-5 py-4">
                    <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
                </td>
                <td class="px-5 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $statusStyle }}">{{ ucfirst($booking->status_bookings) }}</span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.bookings.show', $booking) }}"
                           class="text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-16 text-center text-gray-400">
                <i class="fas fa-clipboard-list text-4xl mb-3 opacity-30"></i>
                <p>Belum ada booking</p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($bookings->hasPages())
<div class="mt-5 flex justify-center">{{ $bookings->appends(request()->query())->links() }}</div>
@endif
@endsection
