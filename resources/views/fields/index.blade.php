@extends('layouts.app')

@section('title', 'Cari Lapangan - ActiveCourt')

@section('content')
    <div class="bg-slate-50 min-h-screen py-8 relative overflow-hidden">
        <!-- Decorative background elements -->
        <div
            class="absolute top-[-10%] left-[-5%] w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse pointer-events-none z-0">
        </div>
        <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse pointer-events-none z-0"
            style="animation-delay: 2s;"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none z-0">
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-800">Cari Lapangan</h1>
                <p class="text-slate-500 mt-1">Temukan lapangan olahraga terbaik di kotamu</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filter Panel - Sidebar -->
                <div class="w-full lg:w-72 flex-shrink-0" x-data="{ filtersOpen: false }">
                    <!-- Mobile Filter Toggle -->
                    <button @click="filtersOpen = !filtersOpen"
                        class="lg:hidden w-full flex items-center justify-between bg-white/80 backdrop-blur-md border border-white/50 shadow-sm rounded-xl px-4 py-3 mb-4">
                        <span class="font-semibold text-slate-700"><i
                                class="fas fa-filter mr-2 text-accent-500"></i>Filters</span>
                        <i class="fas" :class="filtersOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>

                    <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 shadow-xl p-6"
                        :class="filtersOpen ? 'block' : 'hidden lg:block'">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                                <i class="fas fa-sliders-h text-accent-500"></i>
                                Filters
                            </h3>
                            <a href="{{ route('fields.index') }}"
                                class="text-sm text-accent-600 hover:text-accent-700 font-medium">Hapus Filter</a>
                        </div>

                        <form method="GET" action="{{ route('fields.index') }}" class="space-y-6" id="filter-form">
                            <!-- City / Location -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">City or area</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" name="location" value="{{ request('location') }}"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition shadow-inner"
                                        placeholder="Cari kota atau area...">
                                </div>
                            </div>

                            <!-- Sport Type -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Sports</label>
                                <div class="space-y-2">
                                    @php
                                        $sports = ['Futsal', 'Badminton', 'Basket', 'Voli', 'Tenis'];
                                        $selectedSports = request('sports', []);
                                        $otherSports = array_diff((array) $selectedSports, $sports);
                                        $hasOther = count($otherSports) > 0;
                                        $otherValue = $hasOther ? reset($otherSports) : '';
                                    @endphp
                                    <div x-data="{ showOther: {{ $hasOther ? 'true' : 'false' }} }">
                                        @foreach ($sports as $sport)
                                            <label class="flex items-center gap-2 cursor-pointer group mb-2">
                                                <input type="checkbox" name="sports[]" value="{{ $sport }}"
                                                    class="w-4 h-4 rounded border-slate-300 text-accent-500 focus:ring-accent-500"
                                                    {{ in_array($sport, (array) $selectedSports) ? 'checked' : '' }}>
                                                <span
                                                    class="text-sm text-slate-600 group-hover:text-slate-800">{{ $sport }}</span>
                                            </label>
                                        @endforeach
                                        
                                        <label class="flex items-center gap-2 cursor-pointer group mt-2">
                                            <input type="checkbox" x-model="showOther" 
                                                class="w-4 h-4 rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                                            <span class="text-sm text-slate-600 group-hover:text-slate-800">Lainnya (Tulis Sendiri)</span>
                                        </label>
                                        <div x-show="showOther" class="mt-2 pl-6">
                                            <input type="text" :name="showOther ? 'sports[]' : ''" value="{{ $otherValue }}"
                                                class="w-full text-sm py-1.5 px-3 bg-white/80 border border-slate-200 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition" 
                                                placeholder="Ketik tipe olahraga..." 
                                                :disabled="!showOther">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Max Price -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Max Price / hours</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-medium">Rp</span>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition shadow-inner"
                                        placeholder="Maksimal harga" min="0">
                                </div>
                                <!-- Price Range Display -->
                                <div class="mt-3 space-y-2">
                                    @php
                                        $priceRanges = [
                                            ['label' => '< Rp 50.000', 'val' => 50000],
                                            ['label' => 'Rp 50.000 - Rp 100.000', 'val' => 100000],
                                            ['label' => 'Rp 100.000 - Rp 200.000', 'val' => 200000],
                                            ['label' => '> Rp 200.000', 'val' => 999999999],
                                        ];
                                    @endphp
                                    @foreach ($priceRanges as $range)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="max_price_quick" value="{{ $range['val'] }}"
                                                class="w-4 h-4 border-slate-300 text-accent-500 focus:ring-accent-500"
                                                {{ request('max_price') == $range['val'] ? 'checked' : '' }}
                                                onclick="this.form.max_price.value = this.value">
                                            <span
                                                class="text-sm text-slate-600 group-hover:text-slate-800">{{ $range['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white font-bold py-3 rounded-xl transition shadow-lg hover:shadow-accent-500/25">
                                <i class="fas fa-search mr-2"></i>Terapkan Filter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Main Content - Field Grid -->
                <div class="flex-1">
                    <!-- Results Header -->
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-slate-500 text-sm">
                            Menampilkan <span class="font-semibold text-slate-700">{{ $fields->count() }}</span> lapangan
                        </p>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-slate-500">Urutkan:</label>
                            <select name="sort" form="filter-form" onchange="document.getElementById('filter-form').submit()"
                                class="text-sm border border-slate-200 bg-white/80 backdrop-blur-sm shadow-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                                <option value="Harga Terendah" {{ request('sort') == 'Harga Terendah' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="Harga Tertinggi" {{ request('sort') == 'Harga Tertinggi' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="Terpopuler" {{ request('sort') == 'Terpopuler' ? 'selected' : '' }}>Terpopuler</option>
                                <option value="Terbaru" {{ request('sort') == 'Terbaru' ? 'selected' : '' }}>Terbaru</option>
                            </select>
                        </div>
                    </div>

                    @if ($fields->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($fields as $field)
                                <div
                                    class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 group flex flex-col">
                                    <div class="relative h-48 bg-slate-200 overflow-hidden">
                                        <img src="{{ $field->image ? asset('storage/' . $field->image) : 'https://images.unsplash.com/photo-1544919982-b61976f0ba43?auto=format&fit=crop&q=80&w=800' }}"
                                            alt="{{ $field->name_fields }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-in-out">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                        </div>
                                        <div
                                            class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-xl shadow-sm border border-white/20">
                                            <span
                                                class="text-xs font-bold text-primary-700">{{ $field->type_fields }}</span>
                                        </div>
                                        <div class="absolute top-4 right-4">
                                            <span
                                                class="bg-green-500/90 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow-sm border border-green-400/50 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                                                Available
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="text-lg font-bold text-slate-800 mb-1.5">{{ $field->name_fields }}</h3>
                                        <p class="text-slate-400 text-sm mb-3 flex items-center gap-1">
                                            <i class="fas fa-map-marker-alt text-accent-500 text-xs"></i>
                                            {{ Str::limit($field->address, 50) }}
                                        </p>
                                        <div class="flex items-center gap-3 text-xs text-slate-500 mb-4">
                                            <span class="flex items-center gap-1"><i class="fas fa-users"></i>
                                                {{ $field->capacity }} org</span>
                                            <span class="flex items-center gap-1"><i class="fas fa-clock"></i> Per
                                                Jam</span>
                                        </div>
                                        <div
                                            class="flex items-center justify-between pt-4 border-t border-slate-200/60 mt-auto">
                                            <div>
                                                <span class="text-xs text-slate-400">Mulai dari</span>
                                                <div>
                                                    <span class="text-lg font-extrabold text-primary-700">Rp
                                                        {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                                                    <span class="text-xs text-slate-400">/jam</span>
                                                </div>
                                            </div>
                                            <a href="{{ Auth::check() ? route('fields.show', $field->id_fields) : route('login') }}"
                                                class="bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-md hover:shadow-accent-500/25 flex items-center gap-1">
                                                Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $fields->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div
                            class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-12 text-center shadow-xl">
                            <div class="text-7xl mb-6">🔍</div>
                            <h3 class="text-xl font-bold text-slate-700 mb-2">Tidak ada lapangan ditemukan</h3>
                            <p class="text-slate-500 mb-6">Coba ubah filter pencarian atau hapus filter yang ada</p>
                            <a href="{{ route('fields.index') }}"
                                class="bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white font-bold px-8 py-3 rounded-xl transition shadow-lg inline-flex items-center gap-2">
                                <i class="fas fa-times"></i>
                                Hapus Semua Filter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
