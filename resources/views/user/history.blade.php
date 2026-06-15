@extends('layouts.app')

@section('title', 'Bookings History - ActiveCourt')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Bookings History</h1>
                <p class="text-slate-500 text-sm mt-1">Manage your upcoming and past court reservations.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-filter text-slate-400"></i> Filter
                </button>
                <button class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-download text-slate-400"></i> Export
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-6">BOOKING ID</th>
                            <th class="py-4 px-6">FIELD NAME</th>
                            <th class="py-4 px-6">DATE & TIME</th>
                            <th class="py-4 px-6">PRICE</th>
                            <th class="py-4 px-6">STATUS</th>
                            <th class="py-4 px-6 text-right">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-slate-50/50 transition group">
                                <td class="py-4 px-6 font-semibold text-slate-700">{{ $booking->booking_code }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                                            <i class="fas fa-volleyball-ball text-xs"></i>
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ $booking->schedule->field->name_fields ?? 'Unknown Field' }}</span>
                                    </div>
                                </td>
                                @php
                                    $duration = 1;
                                    if ($booking->schedule && $booking->schedule->field && $booking->schedule->field->price_per_hour > 0) {
                                        $duration = round($booking->total_price / $booking->schedule->field->price_per_hour);
                                    }
                                    $startTime = \Carbon\Carbon::parse($booking->schedule->start_time ?? '00:00');
                                    $endTime = $startTime->copy()->addHours($duration);
                                @endphp
                                <td class="py-4 px-6 text-slate-600">
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($booking->play_date)->format('d M Y') }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }} ({{$duration}} jam)</div>
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                <td class="py-4 px-6">
                                    @if($booking->status_bookings === 'Paid')
                                        <span class="px-3 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-full border border-green-200">Paid</span>
                                    @elseif($booking->status_bookings === 'Pending')
                                        <span class="px-3 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-full border border-amber-200">Pending</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full border border-red-200">Cancelled</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($booking->status_bookings === 'Paid')
                                        <button class="text-slate-500 hover:text-primary-600 font-semibold text-sm transition">View Details</button>
                                    @elseif($booking->status_bookings === 'Pending')
                                        <button class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded text-sm font-semibold transition border border-red-100">Cancel Booking</button>
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('booking.cancel', $booking->id_bookings) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                                                @csrf
                                                <input type="hidden" name="cancel_reason" value="Dibatalkan oleh pengguna">
                                                <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-red-100">Batal</button>
                                            </form>
                                            <a href="{{ route('booking.checkout', $booking->id_bookings) }}" class="bg-accent-50 text-accent-600 hover:bg-accent-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-accent-100">Bayar</a>
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-medium text-sm">Unavailable</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-8 text-slate-500">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <span class="text-sm text-slate-500">Showing {{ $bookings->count() }} bookings</span>
                </div>
        </div>
    </div>
</div>
@endsection