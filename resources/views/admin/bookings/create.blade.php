@extends('layouts.admin')
@section('title','Booking Offline')
@section('page-title','Buat Booking Offline')
@section('page-subtitle','Buat booking langsung untuk pengunjung yang datang secara offline.')

@section('content')

<div class="max-w-4xl">
    <a href="{{ route('admin.bookings.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary mb-6 transition">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Booking
    </a>

    {{-- Step 1: Pilih Lapangan & Tanggal --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
        <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
            <span class="w-6 h-6 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center">1</span>
            Pilih Lapangan & Tanggal
        </h2>
        <p class="text-gray-400 text-xs mb-5 ml-8">Tentukan lapangan dan tanggal bermain untuk melihat slot yang tersedia.</p>

        <form method="GET" action="{{ route('admin.bookings.offline.create') }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lapangan</label>
                <select name="field_id" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                    <option value="">-- Pilih Lapangan --</option>
                    @foreach($fields as $f)
                        <option value="{{ $f->id_fields }}"
                            {{ (request('field_id') == $f->id_fields || optional($selectedField)->id_fields == $f->id_fields) ? 'selected' : '' }}>
                            {{ $f->name_fields }} ({{ $f->type_fields }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-40">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Main</label>
                <input type="date" name="date" value="{{ request('date') }}" min="{{ date('Y-m-d') }}" required
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="bg-primary hover:bg-blue-800 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition flex items-center gap-2">
                    <i class="fas fa-search"></i> Cari Jadwal
                </button>
            </div>
        </form>
    </div>

    {{-- Step 2 & 3: Jadwal + Info Pelanggan (hanya tampil jika sudah search) --}}
    @if($selectedField)
    <form method="POST" action="{{ route('admin.bookings.offline.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Slot Jadwal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center">2</span>
                    Pilih Slot Jadwal
                </h2>
                <p class="text-gray-400 text-xs mb-4 ml-8">
                    <strong class="text-gray-700">{{ $selectedField->name_fields }}</strong>
                    — {{ \Carbon\Carbon::parse(request('date'))->translatedFormat('l, d F Y') }}
                </p>

                @if($schedules->isEmpty())
                    <div class="text-center py-10 text-gray-400">
                        <i class="fas fa-calendar-times text-3xl mb-2 opacity-40"></i>
                        <p class="text-sm">Tidak ada slot tersedia untuk tanggal ini.</p>
                    </div>
                @else
                    <div class="grid grid-cols-3 gap-2" id="slotGrid">
                        @foreach($schedules as $slot)
                        <label class="slot-label cursor-pointer">
                            <input type="radio" name="schedule_id" value="{{ $slot->id_schedules }}"
                                   class="sr-only slot-radio" required>
                            <div class="slot-card border-2 border-green-200 bg-green-50 text-green-700 rounded-xl py-3 text-center text-xs font-bold transition hover:border-green-400 hover:bg-green-100">
                                {{ substr($slot->start_time,0,5) }}<br>
                                <span class="text-green-500 font-normal">– {{ substr($slot->end_time,0,5) }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div id="slotSummary" class="hidden mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm">
                        <p class="text-blue-800 font-semibold">
                            <i class="fas fa-check-circle text-blue-500 mr-1"></i>
                            Slot dipilih: <span id="slotTime" class="font-bold"></span>
                        </p>
                        <p class="text-blue-600 text-xs mt-0.5">
                            Total: <span id="slotTotal" class="font-bold"></span>
                        </p>
                    </div>

                    <input type="hidden" name="field_id" value="{{ $selectedField->id_fields }}">
                    <input type="hidden" name="date" value="{{ request('date') }}">
                @endif
            </div>

            {{-- Info Pelanggan + Pembayaran --}}
            <div class="space-y-5">

                {{-- Info Pelanggan --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center">3</span>
                        Info Pelanggan
                    </h2>
                    <p class="text-gray-400 text-xs mb-4 ml-8">Pilih pelanggan terdaftar atau masukkan data pengunjung baru.</p>

                    {{-- Toggle --}}
                    <div class="flex rounded-xl border border-gray-200 overflow-hidden mb-4 text-sm font-semibold">
                        <label class="flex-1 text-center cursor-pointer">
                            <input type="radio" name="customer_type" value="existing" class="sr-only customer-toggle"
                                {{ old('customer_type','existing') === 'existing' ? 'checked' : '' }}>
                            <span class="block py-2.5 transition customer-tab-existing {{ old('customer_type','existing') === 'existing' ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                                <i class="fas fa-user-check mr-1"></i> Pelanggan Terdaftar
                            </span>
                        </label>
                        <label class="flex-1 text-center cursor-pointer border-l border-gray-200">
                            <input type="radio" name="customer_type" value="walkin" class="sr-only customer-toggle"
                                {{ old('customer_type') === 'walkin' ? 'checked' : '' }}>
                            <span class="block py-2.5 transition customer-tab-walkin {{ old('customer_type') === 'walkin' ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                                <i class="fas fa-user-plus mr-1"></i> Walk-in / Tamu Baru
                            </span>
                        </label>
                    </div>

                    {{-- Existing User --}}
                    <div id="existingPanel" class="{{ old('customer_type') === 'walkin' ? 'hidden' : '' }}">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cari Pengguna Terdaftar</label>
                        <select name="user_id" id="userSelect"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                            <option value="">-- Pilih Pengguna --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id_users }}" {{ old('user_id') == $u->id_users ? 'selected' : '' }}>
                                    {{ $u->name_users }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Walk-in --}}
                    <div id="walkinPanel" class="{{ old('customer_type') === 'walkin' ? '' : 'hidden' }}">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Pengunjung <span class="text-red-500">*</span></label>
                                <input type="text" name="guest_name" value="{{ old('guest_name') }}"
                                       placeholder="Nama lengkap pengunjung"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('guest_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor HP</label>
                                <input type="text" name="guest_phone" value="{{ old('guest_phone') }}"
                                       placeholder="cth. 081234567890"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            Data tamu baru akan otomatis tersimpan sebagai akun pengguna baru.
                        </p>
                    </div>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center">4</span>
                        Metode Pembayaran
                    </h2>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach([
                            ['Tunai','fas fa-money-bill-wave','cash'],
                            ['Transfer Bank','fas fa-university','bank_transfer'],
                            ['QRIS','fas fa-qrcode','qris'],
                            ['Debit/Kredit','fas fa-credit-card','card'],
                        ] as $pm)
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="{{ $pm[2] }}"
                                   class="sr-only pm-radio" {{ old('payment_method','cash') === $pm[2] ? 'checked' : '' }}>
                            <div class="pm-card border-2 rounded-xl px-3 py-2.5 flex items-center gap-2 text-sm font-semibold transition
                                {{ old('payment_method','cash') === $pm[2] ? 'border-primary bg-blue-50 text-primary' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <i class="{{ $pm[0] }} text-base shrink-0"></i>{{ $pm[0] }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('payment_method')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Catatan tambahan untuk booking ini..."
                                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Submit --}}
                @if(!$schedules->isEmpty())
                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle text-lg"></i>
                    Konfirmasi & Buat Booking
                </button>
                <p class="text-xs text-gray-400 text-center">
                    Booking akan langsung berstatus <strong>Confirmed</strong> dan pembayaran <strong>Berhasil</strong>.
                </p>
                @endif
            </div>
        </div>
    </form>
    @endif
</div>

@push('scripts')
<script>
    const pricePerHour = {{ optional($selectedField)->price_per_hour ?? 0 }};

    // ── Slot picker ──────────────────────────────────
    document.querySelectorAll('.slot-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            // Reset all cards
            document.querySelectorAll('.slot-card').forEach(card => {
                card.className = 'slot-card border-2 border-green-200 bg-green-50 text-green-700 rounded-xl py-3 text-center text-xs font-bold transition hover:border-green-400 hover:bg-green-100';
            });

            // Highlight selected
            const card = this.closest('.slot-label').querySelector('.slot-card');
            card.className = 'slot-card border-2 border-primary bg-blue-50 text-primary rounded-xl py-3 text-center text-xs font-bold transition';

            // Parse time from the card text
            const text = card.textContent.trim().split('–');
            const start = text[0].trim();
            const end   = text[1]?.trim() ?? '';

            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = (end || '00:00').split(':').map(Number);
            const duration = ((eh * 60 + em) - (sh * 60 + sm)) / 60;
            const total    = duration * pricePerHour;

            document.getElementById('slotTime').textContent    = start + ' – ' + end;
            document.getElementById('slotTotal').textContent   = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('slotSummary').classList.remove('hidden');
        });
    });

    // ── Customer type toggle ────────────────────────
    document.querySelectorAll('.customer-toggle').forEach(radio => {
        radio.addEventListener('change', function () {
            const isWalkin = this.value === 'walkin';
            document.getElementById('existingPanel').classList.toggle('hidden', isWalkin);
            document.getElementById('walkinPanel').classList.toggle('hidden', !isWalkin);
            document.getElementById('userSelect').required = !isWalkin;

            // Update tab styling
            ['existing','walkin'].forEach(type => {
                const span = document.querySelector(`.customer-tab-${type}`);
                if (type === this.value) {
                    span.className = span.className.replace('text-gray-500 hover:bg-gray-50','').trim() + ' bg-primary text-white';
                } else {
                    span.className = span.className.replace('bg-primary text-white','').trim() + ' text-gray-500 hover:bg-gray-50';
                }
            });
        });
    });

    // ── Payment method highlight ─────────────────────
    document.querySelectorAll('.pm-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.pm-card').forEach(card => {
                card.className = card.className
                    .replace('border-primary bg-blue-50 text-primary','')
                    .replace('border-gray-200 text-gray-600','')
                    .trim() + ' border-gray-200 text-gray-600';
            });
            const selected = this.closest('label').querySelector('.pm-card');
            selected.className = selected.className
                .replace('border-gray-200 text-gray-600','')
                .trim() + ' border-primary bg-blue-50 text-primary';
        });
    });
</script>
@endpush
@endsection
