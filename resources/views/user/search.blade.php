@extends('layouts.user')
@section('title', 'Cari Lapangan')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Available Fields</h1>
    <p class="text-gray-500 text-sm mt-1">Temukan dan pesan lapangan olahraga pilihanmu</p>
</div>

<div class="flex flex-col lg:flex-row gap-8">

    <!-- Filters Sidebar -->
    <aside class="w-full lg:w-64 flex-shrink-0">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h3 class="font-bold text-gray-900 mb-5">Filters</h3>
            <form method="GET" action="{{ route('user.fields.search') }}" id="filterForm">

                <!-- Location -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="City or area..."
                               class="w-full border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <!-- Sport Type -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sport Type</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="radio" name="type" value="" class="text-primary accent-blue-600"
                                {{ !request('type') ? 'checked' : '' }}>
                            Semua
                        </label>
                        @foreach($sportTypes as $type)
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="radio" name="type" value="{{ $type }}" class="text-primary accent-blue-600"
                                {{ request('type') === $type ? 'checked' : '' }}>
                            {{ $type }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Date -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Main</label>
                    <input type="date" name="date" value="{{ request('date') }}" min="{{ date('Y-m-d') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <!-- Max Price -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max Price / Hour</label>
                    <div class="flex gap-2 mb-2">
                        @foreach([50000, 200000, 500000] as $p)
                        <button type="button"
                            onclick="setPrice({{ $p }})"
                            class="price-btn flex-1 text-xs py-1.5 rounded-lg border transition
                                {{ request('max_price') == $p ? 'border-primary text-primary bg-blue-50' : 'border-gray-200 text-gray-500 hover:border-primary' }}">
                            {{ number_format($p/1000) }}rb
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="max_price" id="maxPriceInput" value="{{ request('max_price') }}">
                </div>

                <button type="submit"
                    class="w-full bg-primary hover:bg-blue-800 text-white py-2.5 rounded-xl font-semibold text-sm transition">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search','type','date','max_price']))
                <a href="{{ route('user.fields.search') }}"
                   class="block text-center text-sm text-red-500 hover:text-red-700 mt-3">
                    <i class="fas fa-times mr-1"></i>Hapus Filter
                </a>
                @endif
            </form>
        </div>
    </aside>

    <!-- Results -->
    <div class="flex-1">
        <div class="flex items-center justify-between mb-5">
            <p class="text-gray-500 text-sm">Menampilkan <strong class="text-gray-900">{{ $fields->total() }}</strong> hasil</p>
        </div>

        @if($fields->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 py-20 text-center">
            <i class="fas fa-search text-5xl text-gray-200 mb-4"></i>
            <p class="text-gray-500 font-semibold">Lapangan tidak ditemukan</p>
            <p class="text-gray-400 text-sm mt-1">Coba ubah filter pencarian Anda</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($fields as $field)
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition hover:-translate-y-1 duration-200">
                <div class="relative">
                    @if($field->image)
                        <img src="{{ asset('storage/'.$field->image) }}" alt="{{ $field->name_fields }}" class="w-full h-44 object-cover">
                    @else
                        <div class="w-full h-44 bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center">
                            <i class="fas fa-futbol text-white text-4xl opacity-30"></i>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full">Available</span>
                    @if($field->type_fields)
                    <span class="absolute top-3 right-3 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded-lg">{{ $field->type_fields }}</span>
                    @endif
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-900 mb-1">{{ $field->name_fields }}</h3>
                    <p class="text-gray-500 text-xs mb-1 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>
                        {{ Str::limit($field->description, 45) }}
                    </p>
                    <p class="text-xs text-gray-400 mb-4">
                        <i class="fas fa-users mr-1"></i>{{ $field->capacity }} orang
                    </p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-primary font-bold text-base">
                                Rp {{ number_format($field->price_per_hour,0,',','.') }}
                            </span>
                            <span class="text-gray-400 text-xs">/hr</span>
                        </div>
                        <a href="{{ route('user.fields.show', $field) }}"
                           class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-3 py-2 rounded-lg transition">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($fields->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $fields->appends(request()->query())->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
function setPrice(val) {
    document.getElementById('maxPriceInput').value = val;
    document.querySelectorAll('.price-btn').forEach(btn => {
        btn.classList.remove('border-primary','text-primary','bg-blue-50');
        btn.classList.add('border-gray-200','text-gray-500');
    });
    event.target.classList.add('border-primary','text-primary','bg-blue-50');
    document.getElementById('filterForm').submit();
}
</script>
@endpush
@endsection