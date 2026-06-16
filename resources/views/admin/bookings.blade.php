@extends('layouts.admin')

@section('title', 'Bookings - ActiveCourt')
@section('page-title', 'Bookings Management')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif
@if(session('info'))
<div class="bg-blue-100 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-info-circle"></i> {{ session('info') }}
</div>
@endif

<!-- Booking Filters -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Cari booking..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500">
        </div>
        <select class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500">
            <option>All Status</option>
            <option>Pending</option>
            <option>Paid</option>
            <option>Cancelled</option>
        </select>
        <input type="date" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500">
        <button class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-4 py-2 rounded-lg transition shadow-sm">
            Filter
        </button>
    </div>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50">
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Booking ID</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Field</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Date & Time</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Payment</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings ?? [] as $booking)
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-4 px-6">
                        <span class="font-bold text-primary-700">{{ $booking->booking_code }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($booking->user->name_users ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-slate-700">{{ $booking->user->name_users ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600">{{ $booking->schedule->field->name_fields ?? '-' }}</td>
                    <td class="py-4 px-6">
                        <span class="text-sm text-slate-700">{{ $booking->play_date ? \Carbon\Carbon::parse($booking->play_date)->format('d M Y') : '-' }}</span>
                        <br>
                        <span class="text-xs text-slate-400">
                            {{ $booking->schedule ? substr($booking->schedule->start_time, 0, 5) : '-' }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="font-semibold text-slate-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </td>
                    <td class="py-4 px-6">
                        @if($booking->payment && $booking->payment->status_payments === 'settlement')
                            <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Settlement</span>
                        @elseif($booking->payment && $booking->payment->status_payments === 'pending')
                            <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Pending</span>
                        @elseif($booking->payment && $booking->payment->status_payments === 'failed')
                            <span class="text-xs font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">Failed</span>
                        @else
                            <span class="text-xs text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        @if($booking->status_bookings === 'Paid')
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Paid</span>
                        @elseif($booking->status_bookings === 'Pending')
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Pending</span>
                        @else
                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Cancelled</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.bookings.detail', $booking->id_bookings) }}" class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition flex items-center justify-center" title="Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @if($booking->status_bookings === 'Paid')
                            <a href="{{ route('admin.bookings.invoice', $booking->id_bookings) }}" target="_blank" class="w-8 h-8 bg-primary-50 hover:bg-primary-100 text-primary-600 rounded-lg transition flex items-center justify-center" title="Invoice">
                                <i class="fas fa-file-invoice text-xs"></i>
                            </a>
                            @endif
                            @if($booking->status_bookings === 'Pending')
                            <form action="{{ route('admin.bookings.forcePaid', $booking->id_bookings) }}" method="POST" class="inline" onsubmit="return confirm('Force Paid booking {{ $booking->booking_code }}?')">
                                @csrf
                                <button type="submit" class="w-8 h-8 bg-green-50 hover:bg-green-100 text-green-600 rounded-lg transition flex items-center justify-center" title="Force Paid">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.bookings.cancel', $booking->id_bookings) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan booking {{ $booking->booking_code }}?')">
                                @csrf
                                <button type="submit" class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition flex items-center justify-center" title="Cancel">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center">
                        <div class="text-5xl mb-3">📋</div>
                        <p class="text-slate-500 font-medium">Belum ada data booking</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
