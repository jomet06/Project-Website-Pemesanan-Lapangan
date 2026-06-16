@extends('layouts.user')
@section('title', 'Riwayat Booking')
@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bookings History</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola pemesanan lapangan kamu yang sudah dan akan datang</p>
    </div>
    <a href="{{ route('user.fields.search') }}"
       class="inline-flex items-center gap-2 bg-primary hover:bg-blue-800 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition">
        <i class="fas fa-plus"></i>Booking Baru
    </a>
</div>

@if($bookings->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 py-20 text-center shadow-sm">
        <i class="fas fa-calendar-times text-5xl text-gray-200 mb-4"></i>
        <p class="text-gray-500 font-semibold text-lg mb-1">Belum ada riwayat booking</p>
        <p class="text-gray-400 text-sm mb-6">Mulai pesan lapangan favoritmu sekarang</p>
        <a href="{{ route('user.fields.search') }}"
           class="inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-xl text-sm hover:bg-blue-800 transition">
            <i class="fas fa-search"></i>Cari Lapangan
        </a>
    </div>
@else

<!-- Desktop Table -->
<div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Booking ID</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Field Name</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($bookings as $booking)
            @php
            $statusStyle = [
                'confirmed' => 'bg-green-100 text-green-700',
                'pending'   => 'bg-yellow-100 text-yellow-700',
                'cancelled' => 'bg-red-100 text-red-700',
                'completed' => 'bg-gray-100 text-gray-600',
            ][$booking->status_bookings] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <tr class="hover:bg-gray-50 transition {{ $booking->status_bookings === 'cancelled' ? 'opacity-60' : '' }}">
                <td class="px-5 py-4">
                    <span class="font-mono font-bold text-primary text-sm">#{{ $booking->booking_code }}</span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-futbol text-primary text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm leading-none">{{ $booking->schedule->field->name_fields }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">{{ $booking->schedule->field->type_fields }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4">
                    <p class="text-sm text-gray-900 font-medium">{{ $booking->play_date?->format('d M Y') }}</p>
                    <p class="text-xs text-gray-500">{{ substr($booking->schedule->start_time,0,5) }} – {{ substr($booking->schedule->end_time,0,5) }}</p>
                </td>
                <td class="px-5 py-4">
                    <span class="font-semibold text-gray-900 text-sm">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
                </td>
                <td class="px-5 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $statusStyle }}">
                        {{ ucfirst($booking->status_bookings) }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        @if($booking->status_bookings === 'pending')
                            <a href="{{ route('user.bookings.payment', $booking) }}"
                               class="text-xs font-semibold text-orange-600 hover:text-orange-800 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition">
                                Bayar
                            </a>
                        @elseif($booking->status_bookings === 'confirmed')
                            <a href="{{ route('user.bookings.success', $booking) }}"
                               class="text-xs font-semibold text-primary hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                                View Details
                            </a>
                        @else
                            <span class="text-xs text-gray-400">Unavailable</span>
                        @endif

                        @if($booking->canBeCancelled())
                        <button onclick="openCancelModal({{ $booking->id_bookings }}, '{{ $booking->booking_code }}')"
                                class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                            Cancel Booking
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="md:hidden space-y-4">
    @foreach($bookings as $booking)
    @php
    $statusStyle = [
        'confirmed' => 'bg-green-100 text-green-700',
        'pending'   => 'bg-yellow-100 text-yellow-700',
        'cancelled' => 'bg-red-100 text-red-700',
        'completed' => 'bg-gray-100 text-gray-600',
    ][$booking->status_bookings] ?? 'bg-gray-100 text-gray-600';
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 {{ $booking->status_bookings === 'cancelled' ? 'opacity-60' : '' }}">
        <div class="flex items-start justify-between mb-3">
            <span class="font-mono font-bold text-primary text-sm">#{{ $booking->booking_code }}</span>
            <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $statusStyle }}">{{ ucfirst($booking->status_bookings) }}</span>
        </div>
        <p class="font-semibold text-gray-900 mb-1">{{ $booking->schedule->field->name_fields }}</p>
        <p class="text-sm text-gray-500 mb-3">{{ $booking->play_date?->format('d M Y') }} · {{ substr($booking->schedule->start_time,0,5) }}–{{ substr($booking->schedule->end_time,0,5) }}</p>
        <div class="flex items-center justify-between">
            <span class="font-bold text-gray-900">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
            <div class="flex gap-2">
                @if($booking->status_bookings === 'pending')
                    <a href="{{ route('user.bookings.payment', $booking) }}" class="text-xs font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-lg">Bayar</a>
                @elseif($booking->status_bookings === 'confirmed')
                    <a href="{{ route('user.bookings.success', $booking) }}" class="text-xs font-bold text-primary bg-blue-50 px-3 py-1.5 rounded-lg">Detail</a>
                @endif
                @if($booking->canBeCancelled())
                    <button onclick="openCancelModal({{ $booking->id_bookings }}, '{{ $booking->booking_code }}')"
                            class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded-lg">Cancel</button>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
@if($bookings->hasPages())
<div class="mt-6 flex justify-center">{{ $bookings->links() }}</div>
@endif

@endif

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Batalkan Pemesanan</h3>
                <p id="cancelBookingCode" class="text-sm text-gray-500"></p>
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">
            Tindakan ini tidak dapat dibatalkan. Pembatalan hanya berlaku maksimal H-3 sebelum jadwal bermain.
        </p>
        <form id="cancelForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Pembatalan <span class="text-red-500">*</span></label>
                <textarea name="cancel_reason" rows="3" required minlength="10"
                          placeholder="Jelaskan alasan pembatalan..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
                <p class="text-xs text-gray-400 mt-1">Minimal 10 karakter</p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()"
                        class="flex-1 border border-gray-200 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl text-sm font-bold transition">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openCancelModal(bookingId, bookingCode) {
    document.getElementById('cancelForm').action = `/dashboard/bookings/${bookingId}/cancel`;
    document.getElementById('cancelBookingCode').textContent = '#' + bookingCode;
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
@endpush
@endsection
