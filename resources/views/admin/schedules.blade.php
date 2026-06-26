@extends('layouts.admin')

@section('title', 'Schedule Management - ActiveCourt')
@section('page-title', 'Schedule Management')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('info'))
<div class="bg-blue-100 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-info-circle"></i> {{ session('info') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif
@if($errors->any())
<div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-exclamation-circle"></i> Gagal menambahkan! Silakan periksa kembali isian form Anda.
</div>
@endif

<!-- Add Schedule Form -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <h3 class="font-bold text-slate-800">Tambah Jadwal Baru</h3>
            <p class="text-xs text-slate-500 font-medium">Buat slot jadwal untuk lapangan</p>
        </div>
    </div>

    <form action="{{ route('admin.schedules.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4 items-end">
        @csrf
        <div class="lg:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Lapangan</label>
            <select name="field_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                <option value="">Pilih Lapangan</option>
                @foreach($fields as $field)
                    <option value="{{ $field->id_fields }}">{{ $field->name_fields }} ({{ $field->type_fields }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tgl Mulai</label>
            <input type="date" name="start_date" required min="{{ \Carbon\Carbon::today()->toDateString() }}" 
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tgl Selesai</label>
            <input type="date" name="end_date" required min="{{ \Carbon\Carbon::today()->toDateString() }}" 
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jam Mulai</label>
            <input type="time" name="start_time" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jam Selesai</label>
            <input type="time" name="end_time" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Interval</label>
            <select name="interval" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                <option value="60">60 Menit</option>
                <option value="90">90 Menit</option>
                <option value="120">120 Menit</option>
            </select>
        </div>
        <div class="lg:col-span-7 flex justify-end">
            <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Generate Jadwal
            </button>
        </div>
    </form>
</div>

<!-- Schedules Table -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between px-6 py-4 border-b border-slate-100 gap-4">
        <h3 class="font-bold text-slate-800">Daftar Jadwal</h3>
        <form action="{{ route('admin.schedules') }}" method="GET" class="flex flex-col lg:flex-row items-center gap-2 w-full md:w-auto">
            <select name="filter_field_id" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-accent-500 outline-none w-full sm:w-auto">
                <option value="">Semua Lapangan</option>
                @foreach($fields as $field)
                    <option value="{{ $field->id_fields }}" {{ request('filter_field_id') == $field->id_fields ? 'selected' : '' }}>
                        {{ $field->name_fields }}
                    </option>
                @endforeach
            </select>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input type="date" name="filter_start_date" 
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-accent-500 outline-none w-full"
                       value="{{ request('filter_start_date', \Carbon\Carbon::today()->toDateString()) }}">
                <span class="text-slate-400 font-medium text-sm text-center">s/d</span>
                <input type="date" name="filter_end_date" 
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-accent-500 outline-none w-full"
                       value="{{ request('filter_end_date', \Carbon\Carbon::today()->toDateString()) }}">
            </div>
            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white text-sm font-bold px-4 py-1.5 rounded-lg transition w-full sm:w-auto">Filter</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" id="schedulesTable">
            <thead>
                <tr class="bg-slate-50">
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Field</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Start</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">End</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Booking</th>
                    <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($schedules as $schedule)
                <tr class="hover:bg-slate-50 transition" data-date="{{ $schedule->date }}">
                    <td class="py-4 px-6">
                        <span class="font-semibold text-slate-800">{{ $schedule->field->name_fields ?? '-' }}</span>
                        <br><span class="text-xs text-slate-400">{{ $schedule->field->type_fields ?? '' }}</span>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600">{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</td>
                    <td class="py-4 px-6 font-semibold text-slate-700">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                    <td class="py-4 px-6 font-semibold text-slate-700">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                    <td class="py-4 px-6">
                        @if($schedule->status_schedules === 'Available')
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Available</span>
                        @elseif($schedule->status_schedules === 'Booked')
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Booked</span>
                        @else
                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Locked</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-sm">
                        @if($schedule->booking)
                            <span class="font-medium text-slate-700">{{ $schedule->booking->user->name_users ?? '-' }}</span>
                            <br><span class="text-xs text-slate-400">{{ $schedule->booking->booking_code }}</span>
                        @else
                            <span class="text-slate-400 italic">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            @if(!$schedule->booking || $schedule->booking->status_bookings === 'Cancelled')
                                <form action="{{ route('admin.schedules.toggle', $schedule->id_schedules) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="w-8 h-8 {{ $schedule->status_schedules === 'Available' ? 'bg-amber-50 hover:bg-amber-100 text-amber-600' : 'bg-green-50 hover:bg-green-100 text-green-600' }} rounded-lg transition flex items-center justify-center"
                                            title="{{ $schedule->status_schedules === 'Available' ? 'Lock' : 'Unlock' }}">
                                        <i class="fas fa-{{ $schedule->status_schedules === 'Available' ? 'lock' : 'lock-open' }} text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.schedules.destroy', $schedule->id_schedules) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition flex items-center justify-center" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400 italic">Terbooking</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center">
                        <div class="text-5xl mb-3">📅</div>
                        <p class="text-slate-500 font-medium">Belum ada jadwal</p>
                        <p class="text-slate-400 text-sm mt-1">Buat jadwal baru menggunakan form di atas.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


