@extends('layouts.user')
@section('title','Dashboard')
@section('content')

<!-- Greeting -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name_users }}! 👋</h1>
    <p class="text-gray-500 mt-1">Selamat datang di dashboard pemesanan lapanganmu</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
    $statsArr = [
        ['label'=>'Total Booking','value'=>$stats['total'],'icon'=>'fas fa-clipboard-list','color'=>'bg-blue-50 text-blue-600','iconbg'=>'bg-blue-100'],
        ['label'=>'Dikonfirmasi','value'=>$stats['confirmed'],'icon'=>'fas fa-check-circle','color'=>'bg-green-50 text-green-600','iconbg'=>'bg-green-100'],
        ['label'=>'Menunggu','value'=>$stats['pending'],'icon'=>'fas fa-clock','color'=>'bg-yellow-50 text-yellow-600','iconbg'=>'bg-yellow-100'],
        ['label'=>'Dibatalkan','value'=>$stats['cancelled'],'icon'=>'fas fa-times-circle','color'=>'bg-red-50 text-red-600','iconbg'=>'bg-red-100'],
    ];
    @endphp
    @foreach($statsArr as $s)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 {{ $s['iconbg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="{{ $s['icon'] }} {{ explode(' ',$s['color'])[1] }} text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900">{{ $s['value'] }}</p>
            <p class="text-gray-500 text-xs mt-0.5">{{ $s['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
    <a href="{{ route('user.fields.search') }}"
       class="flex items-center gap-5 bg-gradient-to-r from-primary to-blue-600 rounded-2xl p-6 text-white hover:opacity-95 transition shadow-lg shadow-blue-200">
        <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
            <i class="fas fa-search text-2xl"></i>
        </div>
        <div>
            <p class="font-bold text-lg">Cari Lapangan</p>
            <p class="text-blue-200 text-sm">Temukan & pesan lapangan favoritmu</p>
        </div>
        <i class="fas fa-arrow-right ml-auto text-white opacity-70"></i>
    </a>
    <a href="{{ route('user.bookings.history') }}"
       class="flex items-center gap-5 bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-md transition">
        <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-history text-primary text-2xl"></i>
        </div>
        <div>
            <p class="font-bold text-gray-900 text-lg">Riwayat Booking</p>
            <p class="text-gray-500 text-sm">Lihat semua pesanan kamu</p>
        </div>
        <i class="fas fa-arrow-right ml-auto text-gray-400"></i>
    </a>
</div>

<!-- Recent Bookings -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 text-lg">Booking Terbaru</h2>
        <a href="{{ route('user.bookings.history') }}" class="text-primary text-sm font-medium hover:underline">Lihat Semua</a>
    </div>

    @forelse($recentBookings as $booking)
    <div class="px-6 py-4 border-b border-gray-50 hover:bg-gray-50 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4 min-w-0">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-futbol text-primary text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-gray-900 text-sm truncate">{{ $booking->schedule->field->name_fields }}</p>
                <p class="text-gray-500 text-xs mt-0.5">
                    {{ $booking->play_date?->format('d M Y') }} • {{ $booking->schedule->start_time }} – {{ $booking->schedule->end_time }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            @php
            $statusClasses = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700','completed'=>'bg-gray-100 text-gray-700'];
            @endphp
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusClasses[$booking->status_bookings] ?? 'bg-gray-100 text-gray-700' }}">
                {{ ucfirst($booking->status_bookings) }}
            </span>
            <a href="{{ route('user.bookings.payment', $booking) }}" class="text-primary text-xs hover:underline">Detail</a>
        </div>
    </div>
    @empty
    <div class="px-6 py-12 text-center">
        <i class="fas fa-calendar-times text-4xl text-gray-200 mb-3"></i>
        <p class="text-gray-500">Belum ada booking. <a href="{{ route('user.fields.search') }}" class="text-primary font-semibold">Pesan sekarang!</a></p>
    </div>
    @endforelse
</div>
@endsection