@extends('layouts.app')
@section('title', $field->name_fields . ' - ActiveCourt')
@section('content')

<!-- Breadcrumb -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
        <i class="fas fa-chevron-right mx-2 text-xs text-gray-300"></i>
        <a href="{{ route('guest.fields') }}" class="hover:text-primary">Lapangan</a>
        <i class="fas fa-chevron-right mx-2 text-xs text-gray-300"></i>
        <span class="text-gray-900 font-medium">{{ $field->name_fields }}</span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- LEFT: Field Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image -->
            @if($field->image)
                <img src="{{ asset('storage/'.$field->image) }}" alt="{{ $field->name_fields }}"
                     class="w-full h-72 object-cover rounded-2xl shadow-md">
            @else
                <div class="w-full h-72 rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center">
                    <i class="fas fa-futbol text-white text-6xl opacity-30"></i>
                </div>
            @endif

            <!-- Name + Badge -->
            <div>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $field->name_fields }}</h1>
                        <p class="text-gray-500 text-sm mt-1 flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>
                            {{ $field->description }}
                        </p>
                    </div>
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full">{{ $field->type_fields }}</span>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <i class="fas fa-users text-primary mb-2"></i>
                    <p class="font-bold text-gray-900">{{ $field->capacity }} Orang</p>
                    <p class="text-gray-500 text-xs mt-0.5">Kapasitas</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <i class="fas fa-tag text-primary mb-2"></i>
                    <p class="font-bold text-gray-900">Rp {{ number_format($field->price_per_hour,0,',','.') }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">Per Jam</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <i class="fas fa-circle text-green-500 mb-2"></i>
                    <p class="font-bold text-gray-900">{{ $field->is_active ? 'Aktif' : 'Tidak Aktif' }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">Status</p>
                </div>
            </div>

            <!-- Facilities -->
            @if($field->facilities->count())
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h3 class="font-bold text-gray-900 mb-4">Fasilitas</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($field->facilities as $facility)
                    <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-4 py-2.5">
                        @if($facility->icon)
                            <i class="{{ $facility->icon }} text-primary text-sm"></i>
                        @else
                            <i class="fas fa-check-circle text-green-500 text-sm"></i>
                        @endif
                        <span class="text-gray-700 text-sm">{{ $facility->name_facilities }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Schedules -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h3 class="font-bold text-gray-900 mb-5">Jadwal Tersedia</h3>
                @forelse($schedules as $date => $slots)
                <div class="mb-5">
                    <p class="text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-calendar text-primary mr-2"></i>
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($slots as $slot)
                        <span class="bg-green-50 text-green-700 border border-green-200 text-xs font-semibold px-3 py-1.5 rounded-lg">
                            {{ substr($slot->start_time,0,5) }} – {{ substr($slot->end_time,0,5) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-6">Tidak ada jadwal tersedia saat ini</p>
                @endforelse
            </div>
        </div>

        <!-- RIGHT: Booking Summary (Guest → redirect to login) -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 sticky top-24">
                <h3 class="font-bold text-gray-900 text-lg mb-2">Ringkasan Pemesanan</h3>
                <div class="border-t border-gray-100 pt-4 mb-5">
                    <p class="text-gray-600 text-sm mb-1">Lapangan</p>
                    <p class="font-semibold text-gray-900">{{ $field->name_fields }}</p>
                    <p class="text-gray-600 text-sm mt-3 mb-1">Harga Dasar</p>
                    <p class="font-bold text-primary text-lg">Rp {{ number_format($field->price_per_hour,0,',','.') }}<span class="text-gray-400 text-sm font-normal"> / jam</span></p>
                </div>
                <div class="border-t border-gray-100 pt-4 mb-5">
                    <div class="flex justify-between text-sm text-gray-500 mb-2"><span>Total</span><span class="font-bold text-gray-900">Tergantung jadwal</span></div>
                </div>
                @auth
                    <a href="{{ route('user.fields.show', $field) }}"
                       class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center font-bold py-3 rounded-xl transition">
                        <i class="fas fa-bolt mr-2"></i>Pesan Sekarang
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center font-bold py-3 rounded-xl transition mb-3">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Pesan
                    </a>
                    <a href="{{ route('register') }}"
                       class="block w-full border-2 border-primary text-primary text-center font-bold py-3 rounded-xl hover:bg-primary hover:text-white transition">
                        Daftar Gratis
                    </a>
                    <p class="text-xs text-gray-400 text-center mt-3">Anda harus login untuk melakukan pemesanan</p>
                @endauth

                <!-- Manager -->
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 text-center mb-3">Tanya Pengelola</p>
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                        <div class="w-9 h-9 bg-primary rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">A</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Admin ActiveCourt</p>
                            <p class="text-xs text-gray-500">Pengelola Lapangan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection