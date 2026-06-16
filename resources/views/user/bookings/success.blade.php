@extends('layouts.user')
@section('title', 'Booking Berhasil - ActiveCourt')
@section('content')

<div class="max-w-lg mx-auto">
    <!-- Success Banner -->
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-3xl p-8 text-center text-white mb-6 shadow-xl shadow-green-100">
        <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-white text-4xl"></i>
        </div>
        <h1 class="text-2xl font-extrabold mb-1">Pembayaran Berhasil!</h1>
        <p class="text-green-100 text-sm">Booking Anda telah dikonfirmasi. Sampai jumpa di lapangan!</p>
    </div>

    <!-- E-Ticket Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-5">
        <!-- Ticket header -->
        <div class="bg-dark-nav px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                        <i class="fas fa-basketball text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold">ActiveCourt</p>
                        <p class="text-gray-400 text-xs">E-Ticket Resmi</p>
                    </div>
                </div>
                <span class="bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">CONFIRMED</span>
            </div>
        </div>

        <!-- Dashed separator -->
        <div class="relative py-1">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-100 rounded-full -ml-2"></div>
            <div class="border-t-2 border-dashed border-gray-200 mx-4"></div>
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-100 rounded-full -mr-2"></div>
        </div>

        <!-- Ticket body -->
        <div class="px-6 py-5">
            <h2 class="font-bold text-gray-900 text-xl mb-5">{{ $booking->schedule->field->name_fields }}</h2>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Kode Booking</p>
                    <p class="font-mono font-bold text-primary text-sm">{{ $booking->booking_code }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ ucfirst($booking->status_bookings) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Tanggal Main</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->play_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Waktu</p>
                    <p class="font-semibold text-gray-900 text-sm">
                        {{ substr($booking->schedule->start_time,0,5) }} – {{ substr($booking->schedule->end_time,0,5) }} WIB
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Olahraga</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ $booking->schedule->field->type_fields }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Pemesan</p>
                    <p class="font-semibold text-gray-900 text-sm">{{ auth()->user()->name_users }}</p>
                </div>
            </div>

            @if($booking->payment)
            <div class="bg-gray-50 rounded-xl px-4 py-3 mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-medium text-gray-800">{{ $booking->payment->payment_method ?? 'Online Payment' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Dibayar pada</span>
                    <span class="font-medium text-gray-800">{{ $booking->payment->paid_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}</span>
                </div>
            </div>
            @endif

            <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-4">
                <span class="text-gray-700">Total Dibayar</span>
                <span class="text-primary">Rp {{ number_format($booking->total_price,0,',','.') }}</span>
            </div>
        </div>

        <!-- Ticket footer dashed -->
        <div class="relative py-1">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-100 rounded-full -ml-2"></div>
            <div class="border-t-2 border-dashed border-gray-200 mx-4"></div>
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-100 rounded-full -mr-2"></div>
        </div>
        <div class="px-6 py-4 text-center">
            <p class="text-xs text-gray-400">Tunjukkan tiket ini kepada pengelola lapangan</p>
            <p class="text-xs text-gray-400 mt-0.5">Invoice telah dikirim ke <strong class="text-gray-600">{{ auth()->user()->email }}</strong></p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('user.bookings.history') }}"
           class="flex items-center justify-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold py-3 rounded-xl text-sm transition">
            <i class="fas fa-history"></i>Riwayat Booking
        </a>
        <a href="{{ route('user.fields.search') }}"
           class="flex items-center justify-center gap-2 bg-primary hover:bg-blue-800 text-white font-semibold py-3 rounded-xl text-sm transition">
            <i class="fas fa-search"></i>Cari Lapangan Lain
        </a>
    </div>
</div>
@endsection