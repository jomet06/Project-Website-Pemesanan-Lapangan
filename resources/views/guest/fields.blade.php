@extends('layouts.app')
@section('title','Cari Lapangan - ActiveCourt')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters sidebar -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h3 class="font-bold text-gray-900 text-lg mb-5">Filters</h3>
                
                <form method="GET" action="{{ route('guest.fields') }}" id="filterForm">
                    <!-- Search -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi / Nama</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari lapangan..." class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <!-- Sport Type -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Olahraga</label>
                        <div class="space-y-2">
                            @foreach($sportTypes as $type)
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                <input type="radio" name="type" value="{{ $type }}" class="text-primary"
                                    {{ request('type') === $type ? 'checked' : '' }}>
                                {{ $type }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Max Price -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maks. Harga / Jam</label>
                        <div class="flex gap-2 mb-2">
                            @foreach([50000,200000,500000] as $p)
                            <button type="button" onclick="document.querySelector('[name=max_price]').value={{ $p }};document.getElementById('filterForm').submit()"
                                class="flex-1 text-xs py-1.5 rounded-lg border {{ request('max_price')==$p ? 'border-primary text-primary bg-blue-50' : 'border-gray-200 text-gray-500 hover:border-primary' }}">
                                {{ number_format($p/1000) }}rb
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                    </div>

                    <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-xl font-semibold text-sm hover:bg-primary-dark transition">
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['search','type','max_price']))
                    <a href="{{ route('guest.fields') }}" class="block text-center text-sm text-gray-500 hover:text-red-500 mt-3">
                        <i class="fas fa-times mr-1"></i>Hapus Filter
                    </a>
                    @endif
                </form>
            </div>
        </aside>

        <!-- Results -->
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Available Fields</h2>
                    <p class="text-gray-500 text-sm mt-0.5">Menampilkan {{ $fields->total() }} hasil</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
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
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-gray-900 text-base mb-1">{{ $field->name_fields }}</h3>
                        <p class="text-gray-500 text-sm mb-3 flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt text-xs text-gray-400"></i>
                            {{ Str::limit($field->description, 45) }}
                        </p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-primary font-bold text-base">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                                <span class="text-gray-400 text-xs">/jam</span>
                            </div>
                            @auth
                                <a href="{{ route('user.fields.show', $field) }}"
                                   class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-4 py-2 rounded-lg transition">
                                    Book Now
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-4 py-2 rounded-lg transition">
                                    Book Now
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 py-20 text-center">
                    <i class="fas fa-search text-5xl text-gray-200 mb-4"></i>
                    <p class="text-gray-500 font-medium">Tidak ada lapangan ditemukan</p>
                    <p class="text-gray-400 text-sm mt-1">Coba ubah filter pencarian</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($fields->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $fields->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection