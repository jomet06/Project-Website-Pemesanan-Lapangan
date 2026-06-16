@extends('layouts.user')
@section('title', $field->name_fields . ' - ActiveCourt')
@section('content')

{{-- Breadcrumb --}}
<nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
    <a href="{{ route('user.fields.search') }}" class="hover:text-primary">Lapangan</a>
    <i class="fas fa-chevron-right text-xs text-gray-300"></i>
    <span class="text-gray-800 font-medium">{{ $field->name_fields }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- LEFT --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Image --}}
        @if($field->image)
            <img src="{{ asset('storage/'.$field->image) }}" alt="{{ $field->name_fields }}"
                 class="w-full h-72 object-cover rounded-2xl shadow-md">
        @else
            <div class="w-full h-72 rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center">
                <i class="fas fa-futbol text-white text-6xl opacity-20"></i>
            </div>
        @endif

        {{-- Name & Type --}}
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $field->name_fields }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $field->description }}</p>
            </div>
            @if($field->type_fields)
            <span class="bg-gray-100 text-gray-700 text-sm font-semibold px-3 py-1.5 rounded-full">{{ $field->type_fields }}</span>
            @endif
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <i class="fas fa-users text-primary text-lg mb-1"></i>
                <p class="font-bold text-gray-900">{{ $field->capacity }}</p>
                <p class="text-gray-500 text-xs">Kapasitas</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <i class="fas fa-tag text-primary text-lg mb-1"></i>
                <p class="font-bold text-gray-900">Rp {{ number_format($field->price_per_hour,0,',','.') }}</p>
                <p class="text-gray-500 text-xs">Per Jam</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                @if($hasCourts)
                    <i class="fas fa-layer-group text-primary text-lg mb-1"></i>
                    <p class="font-bold text-gray-900">{{ $courts->count() }}</p>
                    <p class="text-gray-500 text-xs">Court Tersedia</p>
                @else
                    <i class="fas fa-circle text-green-500 text-lg mb-1"></i>
                    <p class="font-bold text-gray-900">Aktif</p>
                    <p class="text-gray-500 text-xs">Status</p>
                @endif
            </div>
        </div>

        {{-- Fasilitas --}}
        @if($field->facilities->count())
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <h3 class="font-bold text-gray-900 mb-3">Fasilitas</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($field->facilities as $fac)
                <span class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200 text-gray-600 text-xs px-3 py-1.5 rounded-lg">
                    @if($fac->icon)<i class="{{ $fac->icon }} text-primary"></i>@endif
                    {{ $fac->name_facilities }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- SCHEDULE PICKER --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-gray-900 mb-5">Jadwal Tersedia</h3>

            @if(empty($dates))
                <p class="text-center text-gray-400 py-8">Tidak ada jadwal tersedia saat ini.</p>
            @else

            {{-- Date Tabs --}}
            <div class="flex flex-wrap gap-1.5 mb-4" id="dayTabs">
                @foreach($dates as $idx => $date)
                @php $d = \Carbon\Carbon::parse($date) @endphp
                <button type="button" onclick="switchDay('{{ $date }}')"
                    class="day-tab px-3 py-1.5 rounded-lg text-xs font-semibold transition
                        {{ $idx === 0 ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    data-date="{{ $date }}">
                    {{ $d->format('D, d M') }}
                </button>
                @endforeach
            </div>

            {{-- Court Tabs (only for multi-court fields like GOR Badminton) --}}
            @if($hasCourts)
            <div class="flex flex-wrap gap-2 mb-5">
                @foreach($courts as $cidx => $court)
                <button type="button" onclick="switchCourt('{{ $court }}')"
                    class="court-tab px-4 py-2 rounded-xl text-sm font-semibold border-2 transition
                        {{ $cidx === 0 ? 'border-primary bg-primary text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}"
                    data-court="{{ $court }}">
                    <i class="fas fa-circle text-xs mr-1 opacity-60"></i>Court {{ $court }}
                </button>
                @endforeach
            </div>
            @endif

            {{-- Non-consecutive alert --}}
            <div id="slotAlert" class="hidden mb-3 bg-amber-50 border border-amber-200 text-amber-800 text-xs px-4 py-2.5 rounded-xl">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle shrink-0"></i>
                    <span id="slotAlertText"></span>
                </div>
            </div>

            {{-- Slot Grid (rendered by JS) --}}
            <div id="slotGrid" class="grid grid-cols-4 sm:grid-cols-6 gap-2 min-h-20"></div>

            {{-- Legend --}}
            <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-200 inline-block"></span>Tersedia</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-500 inline-block"></span>Dipilih</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                Klik jam mulai, lalu klik jam akhir yang berurutan untuk memesan lebih dari 1 jam.
            </p>

            @endif
        </div>
    </div>

    {{-- RIGHT: Sidebar --}}
    <div class="lg:col-span-1">
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 sticky top-24">
            <h3 class="font-bold text-gray-900 text-lg mb-4">Ringkasan Pesanan</h3>

            <div class="mb-4 pb-4 border-b border-gray-100">
                <p class="text-xs text-gray-500 mb-1">Lapangan</p>
                <p class="font-semibold text-gray-900">{{ $field->name_fields }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $field->type_fields }}</p>
            </div>

            <div id="slotInfo" class="mb-4 pb-4 border-b border-gray-100 hidden space-y-2.5">
                @if($hasCourts)
                <div>
                    <p class="text-xs text-gray-500">Court</p>
                    <p id="slotCourt" class="font-semibold text-gray-900 text-sm"></p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-500">Jadwal Dipilih</p>
                    <p id="slotText" class="font-semibold text-gray-900 text-sm"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Durasi</p>
                    <p id="slotDuration" class="font-semibold text-gray-900 text-sm"></p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-xs text-gray-500 mb-1">Harga</p>
                <p class="font-bold text-primary text-xl">
                    Rp {{ number_format($field->price_per_hour,0,',','.') }}
                    <span class="text-gray-400 text-sm font-normal">/ jam</span>
                </p>
            </div>

            <div class="flex justify-between mb-5 text-sm font-semibold">
                <span class="text-gray-600">Total</span>
                <span id="totalPrice" class="text-gray-900">-</span>
            </div>

            <form id="bookingForm" method="POST" action="{{ route('user.bookings.confirm', $field) }}">
                @csrf
                <input type="hidden" name="field_id" value="{{ $field->id_fields }}">
                {{-- schedule_ids[] injected dynamically by JS --}}
                <button type="submit" id="bookBtn" disabled
                    class="w-full py-3 rounded-xl font-bold text-sm bg-gray-200 text-gray-400 cursor-not-allowed">
                    <i class="fas fa-calendar-check mr-2"></i>Pilih Jadwal Dulu
                </button>
            </form>

            <p class="text-xs text-gray-400 text-center mt-3">*Tunduk pada syarat & ketentuan berlaku</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
const scheduleData = @json($scheduleData);
const hasCourts    = {{ $hasCourts ? 'true' : 'false' }};
const pricePerHour = {{ $field->price_per_hour }};
const allDates     = @json($dates);
const allCourts    = @json($courts->values()->all());

let selectedDate   = allDates[0] ?? '';
let selectedCourt  = hasCourts ? String(allCourts[0]) : 'single';
let selectionStart = null;
let selectedIds    = [];

function renderSlots() {
    const grid  = document.getElementById('slotGrid');
    const slots = ((scheduleData[selectedDate] ?? {})[selectedCourt]) ?? [];

    if (slots.length === 0) {
        grid.innerHTML = '<p class="col-span-full text-center text-gray-400 text-sm py-8">Tidak ada slot tersedia.</p>';
        return;
    }

    grid.innerHTML = slots.map(slot => {
        const sel = selectedIds.includes(slot.id);
        const cls = sel
            ? 'bg-blue-500 text-white border-blue-500'
            : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100 hover:border-green-300';
        return `<button type="button"
            class="slot-btn text-xs font-bold py-2.5 rounded-xl border-2 transition ${cls}"
            onclick="clickSlot(${slot.id})"
            data-id="${slot.id}" data-start="${slot.start}" data-end="${slot.end}">
            ${slot.start}
        </button>`;
    }).join('');
}

function clickSlot(slotId) {
    const slots   = ((scheduleData[selectedDate] ?? {})[selectedCourt]) ?? [];
    const clicked = slots.findIndex(s => s.id === slotId);
    if (clicked === -1) return;

    if (selectionStart === null) {
        selectionStart = slotId;
        selectedIds    = [slotId];
    } else if (selectionStart === slotId && selectedIds.length === 1) {
        selectionStart = null;
        selectedIds    = [];
    } else {
        const startIdx = slots.findIndex(s => s.id === selectionStart);
        if (clicked < startIdx) {
            selectionStart = slotId;
            selectedIds    = [slotId];
        } else {
            const range = slots.slice(startIdx, clicked + 1);
            const ok    = range.every((s, i) => i === 0 || s.start === range[i - 1].end);
            if (ok) {
                selectedIds = range.map(s => s.id);
            } else {
                showAlert('Slot harus berurutan tanpa jeda waktu di antaranya. Pilih ulang dari awal.');
                selectionStart = slotId;
                selectedIds    = [slotId];
            }
        }
    }
    renderSlots();
    updateSidebar();
}

function switchDay(date) {
    selectedDate   = date;
    selectionStart = null;
    selectedIds    = [];
    document.querySelectorAll('.day-tab').forEach(t => {
        const on = t.dataset.date === date;
        t.className = 'day-tab px-3 py-1.5 rounded-lg text-xs font-semibold transition '
            + (on ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200');
    });
    renderSlots();
    updateSidebar();
}

function switchCourt(court) {
    selectedCourt  = String(court);
    selectionStart = null;
    selectedIds    = [];
    document.querySelectorAll('.court-tab').forEach(t => {
        const on = t.dataset.court === selectedCourt;
        t.className = 'court-tab px-4 py-2 rounded-xl text-sm font-semibold border-2 transition '
            + (on ? 'border-primary bg-primary text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300');
    });
    renderSlots();
    updateSidebar();
}

function updateSidebar() {
    const infoEl = document.getElementById('slotInfo');
    const btnEl  = document.getElementById('bookBtn');
    const form   = document.getElementById('bookingForm');

    form.querySelectorAll('input[name="schedule_ids[]"]').forEach(el => el.remove());

    if (selectedIds.length === 0) {
        infoEl.classList.add('hidden');
        btnEl.disabled  = true;
        btnEl.className = 'w-full py-3 rounded-xl font-bold text-sm bg-gray-200 text-gray-400 cursor-not-allowed';
        btnEl.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Pilih Jadwal Dulu';
        document.getElementById('totalPrice').textContent = '-';
        return;
    }

    const slots    = ((scheduleData[selectedDate] ?? {})[selectedCourt]) ?? [];
    const first    = slots.find(s => s.id === selectedIds[0]);
    const last     = slots.find(s => s.id === selectedIds[selectedIds.length - 1]);
    const duration = selectedIds.length;
    const total    = duration * pricePerHour;

    if (hasCourts) document.getElementById('slotCourt').textContent = 'Court ' + selectedCourt;
    document.getElementById('slotText').textContent     = first.start + ' – ' + last.end + ' WIB';
    document.getElementById('slotDuration').textContent = duration + ' jam';
    document.getElementById('totalPrice').textContent   = 'Rp ' + total.toLocaleString('id-ID');
    infoEl.classList.remove('hidden');

    selectedIds.forEach(id => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'schedule_ids[]';
        inp.value = id;
        form.appendChild(inp);
    });

    btnEl.disabled  = false;
    btnEl.className = 'w-full py-3 rounded-xl font-bold text-sm transition bg-orange-500 hover:bg-orange-600 text-white cursor-pointer';
    btnEl.innerHTML = '<i class="fas fa-bolt mr-2"></i>Pesan Sekarang';
}

function showAlert(msg) {
    document.getElementById('slotAlertText').textContent = msg;
    const el = document.getElementById('slotAlert');
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}

renderSlots();
updateSidebar();
</script>
@endpush
@endsection
