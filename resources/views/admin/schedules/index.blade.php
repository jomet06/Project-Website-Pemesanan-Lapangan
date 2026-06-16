@extends('layouts.admin')
@section('title','Schedule Management')
@section('page-title','Schedule Management')
@section('page-subtitle','Manage and monitor field time slots.')
@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <!-- Filters -->
    <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-wrap gap-2">
        <select name="field_id" onchange="this.form.submit()"
                class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Semua Lapangan</option>
            @foreach($fields as $f)
                <option value="{{ $f->id_fields }}" {{ request('field_id') == $f->id_fields ? 'selected' : '' }}>{{ $f->name_fields }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
               class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
        <select name="status" onchange="this.form.submit()"
                class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Semua Status</option>
            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
            <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Booked</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
        @if(request()->hasAny(['field_id','date','status']))
        <a href="{{ route('admin.schedules.index') }}" class="text-sm text-red-500 hover:text-red-700 px-3 py-2">
            <i class="fas fa-times mr-1"></i>Reset
        </a>
        @endif
    </form>
    <a href="{{ route('admin.schedules.create') }}"
       class="flex items-center gap-2 bg-primary hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded-xl text-sm transition">
        <i class="fas fa-plus"></i>Tambah Jadwal
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lapangan</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Durasi</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($schedules as $schedule)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-4">
                    <p class="font-semibold text-gray-900 text-sm">{{ $schedule->field->name_fields }}</p>
                    <p class="text-xs text-gray-400">{{ $schedule->field->type_fields }}</p>
                </td>
                <td class="px-5 py-4">
                    <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</p>
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l') }}</p>
                </td>
                <td class="px-5 py-4 text-sm text-gray-900 font-medium">
                    {{ substr($schedule->start_time,0,5) }} – {{ substr($schedule->end_time,0,5) }}
                </td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ $schedule->duration_hours }} jam</td>
                <td class="px-5 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full
                        {{ $schedule->status_schedules === 'available' ? 'bg-green-100 text-green-700' :
                           ($schedule->status_schedules === 'booked' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($schedule->status_schedules) }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.schedules.edit', $schedule) }}"
                           class="text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}"
                              onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">
                <i class="fas fa-calendar text-4xl mb-3 opacity-30"></i>
                <p>Tidak ada jadwal ditemukan</p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($schedules->hasPages())
<div class="mt-5 flex justify-center">{{ $schedules->appends(request()->query())->links() }}</div>
@endif
@endsection
