@extends('layouts.admin')
@section('title', 'Detail Booking #' . $booking->booking_code)
@section('page-title', 'Detail Booking')
@section('page-subtitle', 'Informasi lengkap pemesanan lapangan.')
@section('content')

<div class="mb-4">
    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition">
        <i class="fas fa-arrow-left"></i>Kembali ke Daftar Booking
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-5">
        <!-- Booking Detail -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <span class="font-mono font-bold text-primary text-xl">#{{ $booking->booking_code }}</span>
                    <p class="text-gray-500 text-sm mt-0.5">Dibuat {{ $booking->created_at->diffForHumans() }}</p>
                </div>
                @php
                $statusStyle = ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700','completed'=>'bg-gray-100 text-gray-600'][$booking->status_bookings] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <span class="text-sm font-bold px-3 py-1.5 rounded-full {{ $statusStyle }}">{{ ucfirst($booking->status_bookings) }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">Lapangan</p>
                    <p class="font-semibold text-gray-900">{{ $booking->schedule->field->name_fields ?? '-' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $booking->schedule->field->type_fields ?? '' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">Tanggal Main</p>
                    <p class="font-semibold text-gray-900">{{ $booking->play_date?->format('d M Y') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">Waktu</p>
                    <p class="font-semibold text-gray-900">{{ substr($booking->schedule->start_time ?? '',0,5) }} – {{ substr($booking->schedule->end_time ?? '',0,5) }} WIB</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">Total Pembayaran</p>
                    <p class="font-bold text-primary text-lg">Rp {{ number_format($booking->total_price,0,',','.') }}</p>
                </div>
            </div>

            @if($booking->cancel_reason)
            <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-xs font-semibold text-red-600 mb-1">Alasan Pembatalan</p>
                <p class="text-sm text-red-700">{{ $booking->cancel_reason }}</p>
                <p class="text-xs text-red-500 mt-1">Dibatalkan: {{ $booking->cancelled_at?->format('d M Y, H:i') }}</p>
            </div>
            @endif
        </div>

        <!-- Payment Info -->
        @if($booking->payment)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Informasi Pembayaran</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Order ID</span>
                    <span class="font-mono font-semibold text-gray-900">{{ $booking->payment->midtrans_order_id }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Transaction ID</span>
                    <span class="font-mono text-gray-700">{{ $booking->payment->midtrans_transaction_id ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-semibold text-gray-900">{{ $booking->payment->payment_method ?? 'Belum dibayar' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Status Pembayaran</span>
                    <span class="font-bold text-sm px-2.5 py-0.5 rounded-full
                        {{ $booking->payment->status_payments === 'success' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($booking->payment->status_payments) }}
                    </span>
                </div>
                @if($booking->payment->paid_at)
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Dibayar pada</span>
                    <span class="font-semibold text-gray-900">{{ $booking->payment->paid_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-5">
        <!-- User Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-900 mb-4 text-sm">Informasi Pemesan</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($booking->user->name_users ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $booking->user->name_users ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $booking->user->email ?? '' }}</p>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $booking->user) }}"
               class="block w-full text-center text-sm font-semibold text-primary hover:text-blue-800 bg-blue-50 hover:bg-blue-100 py-2 rounded-xl transition">
                Lihat Profil User
            </a>
        </div>

        <!-- Update Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-900 mb-4 text-sm">Update Status</h3>
            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                @csrf @method('PATCH')
                <select name="status_bookings"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white mb-3">
                    @foreach(['pending','confirmed','cancelled','completed'] as $s)
                    <option value="{{ $s }}" {{ $booking->status_bookings === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-2.5 rounded-xl text-sm transition">
                    <i class="fas fa-save mr-2"></i>Simpan Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
