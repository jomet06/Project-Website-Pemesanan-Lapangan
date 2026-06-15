@extends('layouts.app')
@section('title','ActiveCourt - Booking Lapangan Olahraga')
@section('content')

<!-- HERO -->
<section class="hero-bg min-h-[520px] flex items-center relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-20 right-0 w-96 h-96 bg-blue-500 opacity-5 rounded-full translate-x-1/3"></div>
        <div class="absolute bottom-0 left-20 w-64 h-64 bg-blue-400 opacity-5 rounded-full translate-y-1/2"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 py-20 w-full relative z-10">
        <div class="max-w-2xl">
            <span class="inline-block bg-blue-500 bg-opacity-20 text-blue-300 text-xs font-semibold px-3 py-1 rounded-full mb-5">
                🏆 Platform Booking Olahraga #1
            </span>
            <h1 class="text-white text-5xl sm:text-6xl font-extrabold leading-tight mb-5">
                Secure Your Game.<br>
                <span class="text-blue-400">Own The Court.</span>
            </h1>
            <p class="text-blue-200 text-lg mb-10 leading-relaxed max-w-xl">
                Platform profesional untuk mencari dan memesan lapangan futsal, basket, dan badminton premium dengan ketersediaan real-time.
            </p>

            <!-- Search Bar -->
            <form action="{{ route('guest.fields') }}" method="GET"
                  class="flex flex-col sm:flex-row gap-3 bg-white bg-opacity-10 backdrop-blur p-3 rounded-2xl">
                <div class="relative flex-1">
                    <i class="fas fa-map-marker-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" placeholder="Nama lapangan..."
                           class="w-full bg-white text-gray-900 pl-9 pr-4 py-3 rounded-xl text-sm focus:outline-none">
                </div>
                <select name="type" class="bg-white text-gray-700 px-4 py-3 rounded-xl text-sm focus:outline-none min-w-36">
                    <option value="">Semua Olahraga</option>
                    @foreach($sportTypes as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-semibold text-sm transition">
                    <i class="fas fa-search mr-2"></i>Book Now
                </button>
            </form>
        </div>
    </div>
</section>

<!-- SPORT CATEGORIES -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Choose Your Arena</h2>
                <p class="text-gray-500 text-sm mt-1">Temukan lapangan olahraga favoritmu</p>
            </div>
            <a href="{{ route('guest.fields') }}" class="text-primary text-sm font-semibold hover:text-blue-800">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Sport Grid like designs -->
        @php
            $sportColors = ['Futsal'=>'from-green-800 to-green-600','Basketball'=>'from-orange-800 to-orange-600','Badminton'=>'from-blue-800 to-blue-600'];
            $sportIcons  = ['Futsal'=>'⚽','Basketball'=>'🏀','Badminton'=>'🏸'];
            $grouped = $fields->groupBy('type_fields');
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($sportIcons as $sport => $icon)
            @php $count = $grouped->get($sport, collect())->count(); @endphp
            <a href="{{ route('guest.fields', ['type'=>$sport]) }}"
               class="card-hover relative rounded-2xl overflow-hidden h-52 bg-gradient-to-br {{ $sportColors[$sport] ?? 'from-gray-700 to-gray-600' }} group">
                <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-30 transition"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <p class="text-white text-3xl mb-2">{{ $icon }}</p>
                    <h3 class="text-white text-xl font-bold">{{ $sport }}</h3>
                    <p class="text-white text-opacity-80 text-xs mt-1">{{ $count }}+ Lapangan Tersedia</p>
                </div>
                <div class="absolute top-4 right-4 w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center group-hover:bg-opacity-30 transition">
                    <i class="fas fa-arrow-right text-white text-sm"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- FEATURED FIELDS -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Lapangan Unggulan</h2>
                <p class="text-gray-500 text-sm mt-1">Pilihan terbaik dari member kami</p>
            </div>
            <a href="{{ route('guest.fields') }}" class="text-primary text-sm font-semibold hover:text-blue-800">Lihat Semua</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($fields as $field)
            <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                <div class="relative">
                    @if($field->image)
                        <img src="{{ asset('storage/'.$field->image) }}" alt="{{ $field->name_fields }}" class="w-full h-44 object-cover">
                    @else
                        <div class="w-full h-44 bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center">
                            <i class="fas fa-futbol text-white text-4xl opacity-30"></i>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full">Available</span>
                    <span class="absolute top-3 right-3 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded-lg">{{ $field->type_fields }}</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-900 text-base mb-1">{{ $field->name_fields }}</h3>
                    <p class="text-gray-500 text-sm mb-3 flex items-center">
                        <i class="fas fa-map-marker-alt mr-1.5 text-xs text-gray-400"></i>
                        {{ Str::limit($field->description, 45) }}
                    </p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-primary font-bold text-lg">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                            <span class="text-gray-400 text-xs">/jam</span>
                        </div>
                        @auth
                            <a href="{{ route('user.fields.show', $field) }}"
                               class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                Book Now
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                Book Now
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-16 text-gray-400">
                <i class="fas fa-futbol text-5xl mb-4 opacity-30"></i>
                <p>Belum ada lapangan tersedia</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- FEATURES / WHY US -->
<section class="py-16 bg-dark-nav">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-2">Kenapa Pilih ActiveCourt?</h2>
        <p class="text-gray-400 mb-12">Kemudahan booking lapangan di genggamanmu</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            @foreach([
                ['fas fa-bolt','Booking Instan','Pesan lapangan dalam hitungan detik tanpa antri','text-yellow-400'],
                ['fas fa-shield-alt','Pembayaran Aman','Terintegrasi Midtrans - gateway terpercaya di Indonesia','text-green-400'],
                ['fas fa-calendar-check','Jadwal Real-time','Lihat ketersediaan jadwal secara langsung & akurat','text-blue-400'],
            ] as $f)
            <div class="bg-white bg-opacity-5 rounded-2xl p-8 text-center hover:bg-opacity-10 transition">
                <div class="w-14 h-14 mx-auto mb-5 bg-white bg-opacity-10 rounded-xl flex items-center justify-center">
                    <i class="{{ $f[0] }} {{ $f[3] }} text-2xl"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $f[1] }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed">{{ $f[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-14 bg-primary">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-3">Siap Bermain?</h2>
        <p class="text-blue-200 mb-8">Daftar gratis dan mulai memesan lapangan favoritmu hari ini</p>
        <div class="flex justify-center gap-4">
            <a href="{{ route('register') }}" class="bg-white text-primary font-bold px-8 py-3 rounded-xl hover:bg-gray-100 transition">
                Daftar Gratis
            </a>
            <a href="{{ route('guest.fields') }}" class="border-2 border-white text-white font-bold px-8 py-3 rounded-xl hover:bg-white hover:text-primary transition">
                Lihat Lapangan
            </a>
        </div>
    </div>
</section>
@endsection