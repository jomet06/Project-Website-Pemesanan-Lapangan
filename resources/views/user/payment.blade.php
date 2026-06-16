@extends('layouts.user')
@section('title', 'Pembayaran - ActiveCourt')
@section('content')

<div class="max-w-xl mx-auto">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Pilih Metode Pembayaran</h1>
        <p class="text-gray-500 text-sm mt-1">Selesaikan pembayaran untuk mengkonfirmasi pemesanan Anda</p>
    </div>

    <!-- Progress Steps -->
    <div class="flex items-center mb-8">
        @foreach([['1','Pilih Jadwal',true],['2','Konfirmasi',true],['3','Pembayaran',true],['4','Selesai',false]] as $step)
        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $step[2] ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                    {{ $step[0] }}
                </div>
                <span class="text-xs mt-1 {{ $step[2] ? 'text-primary font-semibold' : 'text-gray-400' }}">{{ $step[1] }}</span>
            </div>
            @if(!$loop->last)
            <div class="flex-1 h-0.5 {{ $step[2] ? 'bg-primary' : 'bg-gray-200' }} mx-2 mb-4"></div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Order info -->
    <div class="bg-gray-50 rounded-2xl border border-gray-200 p-5 mb-5">
        <div class="flex items-center justify-between mb-3">
            <span class="font-bold text-gray-900">{{ $booking->schedule->field->name_fields }}</span>
            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ ucfirst($booking->status_bookings) }}</span>
        </div>
        <div class="text-sm text-gray-500 space-y-1">
            <div class="flex justify-between">
                <span>Kode Booking</span>
                <span class="font-mono font-bold text-gray-800">{{ $booking->booking_code }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal Main</span>
                <span class="font-medium text-gray-800">{{ $booking->play_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu</span>
                <span class="font-medium text-gray-800">{{ substr($booking->schedule->start_time,0,5) }} – {{ substr($booking->schedule->end_time,0,5) }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                <span class="font-semibold text-gray-700">Total Pembayaran</span>
                <span class="font-bold text-primary text-lg">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
            </div>
        </div>
    </div>

    @if($booking->status_bookings === 'confirmed')
        <!-- Already paid -->
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center mb-5">
            <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
            <h3 class="font-bold text-green-900 text-lg mb-1">Pembayaran Berhasil!</h3>
            <p class="text-green-700 text-sm">Booking Anda telah dikonfirmasi</p>
        </div>
        <a href="{{ route('user.bookings.success', $booking) }}"
           class="block w-full bg-primary hover:bg-blue-800 text-white text-center font-bold py-3 rounded-xl transition">
            <i class="fas fa-ticket-alt mr-2"></i>Lihat E-Ticket
        </a>
    @elseif($booking->status_bookings === 'cancelled')
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center mb-5">
            <i class="fas fa-times-circle text-red-500 text-4xl mb-3"></i>
            <h3 class="font-bold text-red-900 text-lg mb-1">Pembayaran Dibatalkan</h3>
            <p class="text-red-700 text-sm">Booking ini telah dibatalkan</p>
        </div>
        <a href="{{ route('user.fields.search') }}"
           class="block w-full bg-primary hover:bg-blue-800 text-white text-center font-bold py-3 rounded-xl transition">
            Cari Lapangan Lain
        </a>
    @else
        <!-- Pay Button via Midtrans Snap -->
        <button id="payBtn"
            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-2xl text-lg transition shadow-lg shadow-orange-200 flex items-center justify-center gap-3">
            <i class="fas fa-credit-card"></i>
            Bayar Sekarang
            <span class="font-bold">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
        </button>

        <p class="text-center text-xs text-gray-400 mt-3">
            <i class="fas fa-lock mr-1"></i>Pembayaran aman & terenkripsi oleh Midtrans
        </p>

        <!-- Midtrans methods logos -->
        <div class="mt-5 p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-center text-gray-500 mb-3">Metode pembayaran tersedia:</p>
            <div class="flex flex-wrap justify-center gap-3 text-xs text-gray-500">
                @foreach(['Transfer Bank','Virtual Account','QRIS','GoPay','OVO','Dana','ShopeePay'] as $m)
                <span class="bg-white border border-gray-200 px-2.5 py-1 rounded-lg">{{ $m }}</span>
                @endforeach
            </div>
        </div>

        <!-- Repeat/cancel -->
        <div class="mt-4 flex gap-3">
            <form method="POST" action="{{ route('user.bookings.cancel', $booking) }}" class="flex-1"
                  onsubmit="return confirm('Batalkan pemesanan ini?')">
                @csrf
                <input type="hidden" name="cancel_reason" value="Dibatalkan oleh pengguna sebelum pembayaran">
                <button class="w-full border border-red-200 text-red-600 hover:bg-red-50 py-2.5 rounded-xl text-sm font-medium transition">
                    <i class="fas fa-times mr-1"></i>Batalkan
                </button>
            </form>
        </div>
    @endif
</div>

@push('scripts')
@if($booking->status_bookings === 'pending' && $payment?->snap_token)
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('payBtn').addEventListener('click', function() {
        snap.pay('{{ $payment->snap_token }}', {
            onSuccess: function(result) {
                window.location.href = '{{ route("user.bookings.success", $booking) }}';
            },
            onPending: function(result) {
                alert('Pembayaran sedang diproses...');
            },
            onError: function(result) {
                alert('Terjadi kesalahan pada pembayaran. Silahkan coba lagi.');
            },
            onClose: function() {
                // User closed the popup
            }
        });
    });
</script>
@endif
@endpush
@endsection