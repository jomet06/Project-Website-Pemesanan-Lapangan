@extends('layouts.user')
@section('title', $field->name_fields . ' - ActiveCourt')
@section('content')

<!-- Breadcrumb -->
<nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
    <a href="{{ route('user.fields.search') }}" class="hover:text-primary">Lapangan</a>
    <i class="fas fa-chevron-right text-xs text-gray-300"></i>
    <span class="text-gray-800 font-medium">{{ $field->name_fields }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- LEFT -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Image -->
        @if($field->image)
            <img src="{{ asset('storage/'.$field->image) }}" alt="{{ $field->name_fields }}"
                 class="w-full h-72 object-cover rounded-2xl shadow-md">
        @else
            <div class="w-full h-72 rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center">
                <i class="fas fa-futbol text-white text-6xl opacity-20"></i>
            </div>
        @endif

        <!-- Name -->
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">PREMIUM FACILITY</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $field->name_fields }}</h1>
                <p class="text-gray-500 text-sm mt-1 flex items-center gap-1.5">
                    <i class="fas fa-map-marker-alt text-xs"></i>{{ $field->description }}
                </p>
            </div>
            <span class="bg-gray-100 text-gray-700 text-sm font-semibold px-3 py-1.5 rounded-full">{{ $field->type_fields }}</span>
        </div>

        <!-- Stats row -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <i class="fas fa-users text-primary text-lg mb-1"></i>
                <p class="font-bold text-gray-900">{{ $field->capacity }}</p>
                <p class="text-gray-500 text-xs">Orang</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <i class="fas fa-tag text-primary text-lg mb-1"></i>
                <p class="font-bold text-gray-900">Rp {{ number_format($field->price_per_hour,0,',','.') }}</p>
                <p class="text-gray-500 text-xs">Per Jam</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <i class="fas fa-circle text-green-500 text-lg mb-1"></i>
                <p class="font-bold text-gray-900">Aktif</p>
                <p class="text-gray-500 text-xs">Status</p>
            </div>
        </div>

        <!-- Informasi Lapangan -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-gray-900 mb-2">Informasi Lapangan</h3>
            <p class="text-gray-600 text-sm leading-relaxed">{{ $field->description }}</p>

            @if($field->facilities->count())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($field->facilities as $f)
                <span class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200 text-gray-600 text-xs px-3 py-1.5 rounded-lg">
                    @if($f->icon)<i class="{{ $f->icon }} text-primary"></i>@endif
                    {{ $f->name_facilities }}
                </span>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Schedule Picker -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-900">Jadwal Tersedia</h3>
                <!-- Day tabs -->
                <div class="flex gap-1" id="dayTabs">
                    @foreach($schedules as $date => $slots)
                    @php $d = \Carbon\Carbon::parse($date) @endphp
                    <button type="button" onclick="switchDay('{{ $date }}')"
                        class="day-tab px-3 py-1.5 rounded-lg text-xs font-semibold transition
                        {{ $loop->first ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        data-date="{{ $date }}">
                        {{ $d->format('d/m') }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Slot grids per date -->
            @forelse($schedules as $date => $slots)
            <div id="day-{{ $date }}" class="day-panel {{ !$loop->first ? 'hidden' : '' }}">
                <p class="text-xs text-gray-500 mb-3">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </p>
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                    @foreach($slots as $slot)
                    <button type="button"
                        onclick="selectSlot({{ $slot->id_schedules }}, '{{ substr($slot->start_time,0,5) }}', '{{ substr($slot->end_time,0,5) }}')"
                        class="slot-btn text-xs font-semibold py-2.5 rounded-xl border-2 border-green-200 bg-green-50 text-green-700 hover:bg-green-500 hover:text-white hover:border-green-500 transition"
                        data-slot="{{ $slot->id_schedules }}">
                        {{ substr($slot->start_time,0,5) }}
                    </button>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-center text-gray-400 text-sm py-8">Tidak ada jadwal tersedia saat ini.</p>
            @endforelse

            <!-- Legend -->
            <div class="mt-4 flex gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-200 inline-block"></span>Tersedia</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-500 inline-block"></span>Dipilih</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-200 inline-block"></span>Sudah Dipesan</span>
            </div>
        </div>
    </div>

    <!-- RIGHT: Summary Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 sticky top-24">
            <h3 class="font-bold text-gray-900 text-lg mb-4">Ringkasan Pesanan</h3>

            <!-- Field info -->
            <div class="mb-4 pb-4 border-b border-gray-100">
                <p class="text-xs text-gray-500 mb-1">Lapangan</p>
                <p class="font-semibold text-gray-900">{{ $field->name_fields }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $field->type_fields }}</p>
            </div>

            <!-- Selected time info -->
            <div id="slotInfo" class="mb-4 pb-4 border-b border-gray-100 hidden">
                <p class="text-xs text-gray-500 mb-1">Jadwal Dipilih</p>
                <p id="slotText" class="font-semibold text-gray-900 text-sm"></p>
                <p class="text-xs text-gray-400 mt-1">
                    Durasi: <span id="slotDuration"></span> jam &nbsp;×&nbsp;
                    Rp {{ number_format($field->price_per_hour,0,',','.') }}
                </p>
            </div>

            <div class="mb-6">
                <p class="text-xs text-gray-500 mb-1">Harga Dasar</p>
                <p class="font-bold text-primary text-xl">
                    Rp {{ number_format($field->price_per_hour,0,',','.') }}
                    <span class="text-gray-400 text-sm font-normal">/ jam</span>
                </p>
            </div>

            <div class="flex justify-between mb-5 text-sm font-semibold">
                <span class="text-gray-600">Total</span>
                <span id="totalPrice" class="text-gray-900">-</span>
            </div>

            <!-- Booking Form -->
            <form id="bookingForm" method="POST" action="{{ route('user.bookings.confirm', $field) }}">
                @csrf
                <input type="hidden" name="schedule_id" id="scheduleIdInput">
                <input type="hidden" name="field_id" value="{{ $field->id_fields }}">

                <button type="submit" id="bookBtn" disabled
                    class="w-full py-3 rounded-xl font-bold text-sm transition
                    bg-gray-200 text-gray-400 cursor-not-allowed"
                    id="bookBtn">
                    <i class="fas fa-calendar-check mr-2"></i>Pilih Jadwal Dulu
                </button>
            </form>

            <p class="text-xs text-gray-400 text-center mt-3">
                *Pemesanan tunduk pada syarat dan ketentuan berlaku
            </p>

            <!-- Contact -->
            <div class="mt-5 pt-4 border-t border-gray-100">
                <button class="w-full flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-medium py-2.5 rounded-xl transition">
                    <i class="fas fa-headset text-primary"></i> Tanya Pengelola
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const pricePerHour = {{ $field->price_per_hour }};
    let selectedSlot = null;

    function switchDay(date) {
        document.querySelectorAll('.day-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('day-' + date)?.classList.remove('hidden');
        document.querySelectorAll('.day-tab').forEach(t => {
            if (t.dataset.date === date) {
                t.classList.add('bg-primary','text-white');
                t.classList.remove('bg-gray-100','text-gray-600');
            } else {
                t.classList.remove('bg-primary','text-white');
                t.classList.add('bg-gray-100','text-gray-600');
            }
        });
    }

    function selectSlot(scheduleId, start, end) {
        // Reset all
        document.querySelectorAll('.slot-btn').forEach(b => {
            b.classList.remove('bg-blue-500','text-white','border-blue-500');
            b.classList.add('bg-green-50','text-green-700','border-green-200');
        });

        // Highlight selected
        const btn = document.querySelector(`[data-slot="${scheduleId}"]`);
        if (btn) {
            btn.classList.remove('bg-green-50','text-green-700','border-green-200');
            btn.classList.add('bg-blue-500','text-white','border-blue-500');
        }

        // Calculate duration
        const startH = parseInt(start.split(':')[0]), startM = parseInt(start.split(':')[1]);
        const endH   = parseInt(end.split(':')[0]),   endM   = parseInt(end.split(':')[1]);
        const duration = ((endH * 60 + endM) - (startH * 60 + startM)) / 60;
        const total = duration * pricePerHour;

        // Update UI
        document.getElementById('scheduleIdInput').value = scheduleId;
        document.getElementById('slotText').textContent = start + ' – ' + end;
        document.getElementById('slotDuration').textContent = duration;
        document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('slotInfo').classList.remove('hidden');

        // Enable button
        const btn2 = document.getElementById('bookBtn');
        btn2.disabled = false;
        btn2.className = 'w-full py-3 rounded-xl font-bold text-sm transition bg-orange-500 hover:bg-orange-600 text-white cursor-pointer';
        btn2.innerHTML = '<i class="fas fa-bolt mr-2"></i>Pesan Sekarang';

        selectedSlot = scheduleId;
    }
</script>
@endpush
@endsection