@extends('layouts.user')
@section('title', 'Konfirmasi Pemesanan')
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Konfirmasi Pemesanan</h1>
        <p class="text-gray-500 text-sm mt-1">Periksa detail pesanan Anda sebelum melanjutkan ke pembayaran</p>
    </div>

    {{-- Progress Steps --}}
    <div class="flex items-center gap-0 mb-8">
        @foreach([['1','Pilih Jadwal',true],['2','Konfirmasi',true],['3','Pembayaran',false],['4','Selesai',false]] as $step)
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

    {{-- Order Summary Card --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden mb-5">
        @if($field->image)
            <img src="{{ asset('storage/'.$field->image) }}" alt="{{ $field->name_fields }}" class="w-full h-40 object-cover">
        @else
            <div class="w-full h-40 bg-gradient-to-br from-blue-900 to-blue-700"></div>
        @endif

        <div class="p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h2 class="font-bold text-gray-900 text-xl">{{ $field->name_fields }}</h2>
                    @if($courtNumber)
                    <p class="text-primary text-sm font-semibold mt-0.5">
                        <i class="fas fa-layer-group mr-1"></i>Court {{ $courtNumber }}
                    </p>
                    @endif
                </div>
                @if($field->type_fields)
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $field->type_fields }}</span>
                @endif
            </div>

            @php
                $firstSchedule = $schedules->first();
                $lastSchedule  = $schedules->last();
                $totalHours    = $schedules->count();
            @endphp

            <div class="space-y-3 border-t border-gray-100 pt-5">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-calendar text-gray-400"></i>Tanggal Main</span>
                    <span class="font-semibold text-gray-900">
                        {{ $firstSchedule->date->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-clock text-gray-400"></i>Waktu</span>
                    <span class="font-semibold text-gray-900">
                        {{ substr($firstSchedule->start_time,0,5) }} – {{ substr($lastSchedule->end_time,0,5) }} WIB
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-hourglass text-gray-400"></i>Durasi</span>
                    <span class="font-semibold text-gray-900">{{ $totalHours }} Jam</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-users text-gray-400"></i>Kapasitas</span>
                    <span class="font-semibold text-gray-900">{{ $field->capacity }} Orang</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-user text-gray-400"></i>Pemesan</span>
                    <span class="font-semibold text-gray-900">{{ auth()->user()->name_users }}</span>
                </div>
            </div>

            {{-- Price breakdown --}}
            <div class="mt-5 pt-4 border-t border-gray-100">
                <div class="flex justify-between text-sm text-gray-500 mb-2">
                    <span>Rp {{ number_format($field->price_per_hour,0,',','.') }} × {{ $totalHours }} jam</span>
                    <span class="text-gray-700">Rp {{ number_format($totalPrice,0,',','.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-3 mt-3">
                    <span class="text-gray-900">Total Pembayaran</span>
                    <span class="text-primary">Rp {{ number_format($totalPrice,0,',','.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Payment --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-6">
        <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-500"></i>Informasi Pembayaran
        </h3>
        <ul class="text-sm text-blue-800 space-y-1.5">
            <li class="flex items-start gap-2"><i class="fas fa-check text-blue-500 mt-0.5 shrink-0"></i>Pembayaran diproses melalui Midtrans (aman & terenkripsi)</li>
            <li class="flex items-start gap-2"><i class="fas fa-check text-blue-500 mt-0.5 shrink-0"></i>Tersedia: Transfer Bank, QRIS, e-Wallet, Virtual Account</li>
            <li class="flex items-start gap-2"><i class="fas fa-check text-blue-500 mt-0.5 shrink-0"></i>E-ticket dikirim ke email setelah pembayaran berhasil</li>
            <li class="flex items-start gap-2"><i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 shrink-0"></i>Pembatalan maksimal H-3 sebelum jadwal bermain</li>
        </ul>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-3">
        <a href="{{ route('user.fields.show', $field) }}"
           class="flex-1 border-2 border-gray-200 text-gray-700 text-center font-semibold py-3 rounded-xl hover:bg-gray-50 transition text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <form method="POST" action="{{ route('user.bookings.store') }}" class="flex-1">
            @csrf
            {{-- All selected schedule IDs --}}
            @foreach($schedules as $s)
            <input type="hidden" name="schedule_ids[]" value="{{ $s->id_schedules }}">
            @endforeach
            <input type="hidden" name="field_id" value="{{ $field->id_fields }}">
            <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition text-sm">
                <i class="fas fa-credit-card mr-2"></i>Lanjut ke Pembayaran
            </button>
        </form>
    </div>
</div>
@endsection
