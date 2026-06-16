@extends('layouts.app')

@section('title', 'Bookings History - ActiveCourt')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Bookings History</h1>
                <p class="text-slate-500 text-sm mt-1">Manage your upcoming and past court reservations.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-filter text-slate-400"></i> Filter
                </button>
                <button class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-download text-slate-400"></i> Export
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-6">BOOKING ID</th>
                            <th class="py-4 px-6">FIELD NAME</th>
                            <th class="py-4 px-6">DATE & TIME</th>
                            <th class="py-4 px-6">PRICE</th>
                            <th class="py-4 px-6">STATUS</th>
                            <th class="py-4 px-6 text-right">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bookings as $booking)
                            @php
                                $schedulesList = $booking->getSchedulesList();
                                $field = $booking->schedule?->field;

                                $createdAt = \Carbon\Carbon::parse($booking->created_at);
                                $expiresAt = $createdAt->copy()->addMinutes(30);
                                $now = \Carbon\Carbon::now();
                                $remainingSeconds = $now->lessThan($expiresAt) ? $now->diffInSeconds($expiresAt, false) : 0;

                                $playDate = \Carbon\Carbon::parse($booking->play_date);
                                $daysToPlay = $now->diffInDays($playDate, false);
                                $canCancelPaid = $booking->status_bookings === 'Paid' && $daysToPlay >= 3;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition group"
                                x-data="timerComponent({{ $remainingSeconds }})"
                                x-init="initTimer()">
                                <td class="py-4 px-6 font-semibold text-slate-700">{{ $booking->booking_code }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                                            <i class="fas fa-volleyball-ball text-xs"></i>
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ $field->name_fields ?? 'Unknown Field' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($booking->play_date)->format('d M Y') }}</div>
                                    @if($schedulesList->isNotEmpty())
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $schedulesList->map(fn($s) => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5))->implode(', ') }} ({{ $schedulesList->count() }} jam)</div>
                                    @else
                                        <div class="text-xs text-slate-400 mt-0.5 italic">Jadwal tidak tersedia</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                <td class="py-4 px-6">
                                    @if($booking->status_bookings === 'Paid')
                                        <span class="px-3 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-full border border-green-200">Paid</span>
                                    @elseif($booking->status_bookings === 'Pending')
                                        <span class="px-3 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-full border border-amber-200">Pending</span>
                                        @if($remainingSeconds > 0)
                                            <div class="text-[11px] text-slate-500 mt-1">
                                                ⏱ <span x-text="display"></span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full border border-red-200">Cancelled</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($booking->status_bookings === 'Paid')
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('booking.invoice', $booking->id_bookings) }}" class="bg-primary-50 text-primary-600 hover:bg-primary-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-primary-100 flex items-center gap-1.5">
                                                <i class="fas fa-file-invoice text-xs"></i> Invoice
                                            </a>
                                            @if($canCancelPaid)
                                                <form action="{{ route('booking.cancel', $booking->id_bookings) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan booking yang sudah dibayar?');">
                                                    @csrf
                                                    <input type="hidden" name="cancel_reason" value="Dibatalkan oleh pengguna">
                                                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-red-100">Batal</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('booking.show', $booking->id_bookings) }}"
                                                class="text-slate-500 hover:text-primary-600 font-semibold text-sm transition ml-2">
                                                    View Details
                                            </a>
                                        </div>
                                    @elseif($booking->status_bookings === 'Pending')
                                        <div class="flex items-center justify-end gap-2">
                                            @if($remainingSeconds > 0)
                                                <form id="cancelForm{{ $booking->id_bookings }}" action="{{ route('booking.cancel', $booking->id_bookings) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="cancel_reason" value="Dibatalkan oleh pengguna">
                                                    <button type="button" onclick="openCancelModal('cancelForm{{ $booking->id_bookings }}')" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-red-100">
                                                        Batal
                                                    </button>
                                                </form>
                                                <a href="{{ route('booking.checkout', $booking->id_bookings) }}" class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition shadow-sm hover:shadow-md flex items-center gap-1.5">
                                                    <i class="fas fa-credit-card text-xs"></i> Bayar
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400 font-medium italic">Waktu habis</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-medium text-sm">Unavailable</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-8 text-slate-500">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <span class="text-sm text-slate-500">Showing {{ $bookings->count() }} bookings</span>
            </div>
        </div>
    </div>
</div>

<div id="cancelModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 shadow-xl w-full max-w-md">
        <h3 class="text-xl font-bold text-slate-800 mb-2">Batalkan Booking</h3>
        <p class="text-slate-500 mb-6">Apakah Anda yakin ingin membatalkan booking ini?</p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeCancelModal()" class="px-4 py-2 border rounded-lg">Kembali</button>
            <button type="button" onclick="submitCancelForm()" class="px-4 py-2 bg-red-500 text-white rounded-lg">Ya, Batalkan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timerComponent', (remainingSeconds) => ({
            remaining: parseInt(remainingSeconds),
            timerId: null,
            get display() {
                if (this.remaining <= 0) return '00:00';
                const mins = Math.floor(this.remaining / 60);
                const secs = this.remaining % 60;
                return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
            },
            initTimer() {
                if (this.remaining <= 0) return;
                this.timerId = setInterval(() => {
                    if (this.remaining > 0) {
                        this.remaining--;
                    } else {
                        clearInterval(this.timerId);
                        location.reload();
                    }
                }, 1000);
            },
            destroy() {
                if (this.timerId) clearInterval(this.timerId);
            }
        }));
    });

    let selectedForm = null;

    function openCancelModal(formId) {
        selectedForm = document.getElementById(formId);
        document.getElementById('cancelModal').classList.remove('hidden');
        document.getElementById('cancelModal').classList.add('flex');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.getElementById('cancelModal').classList.remove('flex');
    }

    function submitCancelForm() {
        if (selectedForm) selectedForm.submit();
    }
</script>
@endpush
