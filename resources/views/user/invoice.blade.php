@extends('layouts.app')

@section('title', 'Invoice - ActiveCourt')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-sm text-slate-500 mb-6 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-primary-600 font-medium transition">Beranda</a>
            <span>&rsaquo;</span>
            <a href="{{ route('user.history') }}" class="hover:text-primary-600 font-medium transition">Riwayat</a>
            <span>&rsaquo;</span>
            <span class="text-primary-700 font-bold">Invoice</span>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" id="invoice-content">
            <!-- Header -->
            <div class="bg-primary-700 px-8 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-receipt"></i> INVOICE
                        </h1>
                        <p class="text-primary-200 text-sm mt-1">ActiveCourt - Platform Sewa Lapangan</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-black text-white">{{ $booking->booking_code }}</p>
                        <p class="text-primary-200 text-xs mt-1">Booking Code</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-8">
                <!-- Status Badge -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Status Pembayaran</h2>
                        <p class="text-sm text-slate-500 mt-1">Invoice ini adalah bukti pembayaran resmi dari ActiveCourt</p>
                    </div>
                    <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-bold rounded-full border border-green-200 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> LUNAS
                    </span>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-slate-50 rounded-lg p-5">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Data Pemesan</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nama</span>
                                <span class="font-semibold text-slate-800">{{ $booking->user->name_users ?? $booking->user->username }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Email</span>
                                <span class="font-semibold text-slate-800">{{ $booking->user->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-5">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Detail Booking</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Booking Code</span>
                                <span class="font-semibold text-primary-700">{{ $booking->booking_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tanggal Booking</span>
                                <span class="font-semibold text-slate-800">{{ $booking->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            @if($booking->payment && $booking->payment->paid_at)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Dibayar Pada</span>
                                <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($booking->payment->paid_at)->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Field Detail -->
                <div class="mb-8">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Detail Lapangan</h3>
                    <div class="bg-slate-50 rounded-lg p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-volleyball-ball text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-800 text-lg">{{ $booking->schedule->field->name_fields ?? '-' }}</h4>
                                <p class="text-sm text-slate-500 mt-0.5">{{ $booking->schedule->field->type_fields ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jadwal -->
                @php
                    $schedulesList = $booking->getSchedulesList();
                    $field = $booking->schedule ? $booking->schedule->field : null;
                @endphp
                <div class="mb-8">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Jadwal Sewa</h3>
                    <div class="bg-slate-50 rounded-lg p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-slate-500 block">Tanggal</span>
                                <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($booking->play_date)->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Waktu</span>
                                <span class="font-bold text-slate-800">
                                    {{ $schedulesList->map(fn($s) => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5))->implode(', ') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Durasi</span>
                                <span class="font-bold text-slate-800">{{ $schedulesList->count() }} Jam</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Detail -->
                <div class="mb-8">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Detail Pembayaran</h3>
                    <div class="bg-slate-50 rounded-lg p-5">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Harga per Jam</span>
                                <span class="font-semibold text-slate-800">Rp {{ number_format($field->price_per_hour ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Jumlah Jam</span>
                                <span class="font-semibold text-slate-800">{{ $schedulesList->count() }} Jam</span>
                            </div>
                            @if($booking->payment && $booking->payment->payment_method)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Metode Pembayaran</span>
                                <span class="font-semibold text-slate-800 capitalize">{{ str_replace('_', ' ', $booking->payment->payment_method) }}</span>
                            </div>
                            @endif
                            @if($booking->payment && $booking->payment->midtrans_transaction_id)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Transaction ID</span>
                                <span class="font-semibold text-slate-800 text-xs">{{ $booking->payment->midtrans_transaction_id }}</span>
                            </div>
                            @endif
                            <div class="border-t border-slate-200 pt-3 flex justify-between">
                                <span class="font-bold text-slate-800 text-base">Total Pembayaran</span>
                                <span class="font-extrabold text-accent-600 text-xl">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-slate-200 pt-6 text-center">
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Invoice ini adalah bukti pembayaran yang sah. <br>
                        ActiveCourt &copy; {{ date('Y') }} - Platform Pemesanan Lapangan Olahraga
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-center gap-4 mt-6">
            <button onclick="window.print()" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-3 rounded-lg transition shadow-md flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak Invoice
            </button>
            <a href="{{ route('user.history') }}" class="bg-white border border-slate-200 text-slate-600 font-bold px-6 py-3 rounded-lg hover:bg-slate-50 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #invoice-content, #invoice-content * { visibility: visible; }
    #invoice-content { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>
@endpush
