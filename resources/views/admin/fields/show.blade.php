@extends('layouts.admin')
@section('title', $field->name_fields)
@section('page-title', $field->name_fields)
@section('page-subtitle', 'Detail & jadwal lapangan')
@section('content')

<div class="mb-4">
    <a href="{{ route('admin.fields.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition">
        <i class="fas fa-arrow-left"></i>Kembali ke Daftar
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Field Info -->
    <div class="lg:col-span-2 space-y-5">
        <!-- Image & Basic Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($field->image)
                <img src="{{ asset('storage/'.$field->image) }}" class="w-full h-52 object-cover">
            @else
                <div class="w-full h-52 bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center">
                    <i class="fas fa-futbol text-white text-5xl opacity-20"></i>
                </div>
            @endif
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="font-bold text-gray-900 text-xl">{{ $field->name_fields }}</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ $field->description }}</p>
                    </div>
                    <div class="flex gap-2 flex-shrink-0 ml-4">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $field->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $field->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-5">
                    <div class="text-center bg-gray-50 rounded-xl p-3">
                        <p class="font-bold text-gray-900">{{ $field->type_fields ?? '-' }}</p>
                        <p class="text-gray-500 text-xs">Jenis</p>
                    </div>
                    <div class="text-center bg-gray-50 rounded-xl p-3">
                        <p class="font-bold text-gray-900">{{ $field->capacity }}</p>
                        <p class="text-gray-500 text-xs">Kapasitas</p>
                    </div>
                    <div class="text-center bg-gray-50 rounded-xl p-3">
                        <p class="font-bold text-primary text-sm">Rp {{ number_format($field->price_per_hour,0,',','.') }}</p>
                        <p class="text-gray-500 text-xs">Per Jam</p>
                    </div>
                </div>

                @if($field->facilities->count())
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">Fasilitas</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($field->facilities as $f)
                        <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-lg font-medium">{{ $f->name_facilities }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Schedules -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Jadwal Terbaru</h3>
                <a href="{{ route('admin.schedules.create') }}?field_id={{ $field->id_fields }}"
                   class="text-sm text-primary font-semibold hover:text-blue-800">
                    <i class="fas fa-plus mr-1"></i>Tambah Jadwal
                </a>
            </div>
            @forelse($field->schedules as $schedule)
            <div class="px-6 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-center w-12">
                        <p class="font-bold text-gray-900 text-sm">{{ \Carbon\Carbon::parse($schedule->date)->format('d') }}</p>
                        <p class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($schedule->date)->format('M') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ substr($schedule->start_time,0,5) }} – {{ substr($schedule->end_time,0,5) }}</p>
                        <p class="text-gray-400 text-xs">{{ $schedule->duration_hours }} jam</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full
                        {{ $schedule->status_schedules === 'available' ? 'bg-green-100 text-green-700' :
                           ($schedule->status_schedules === 'booked' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($schedule->status_schedules) }}
                    </span>
                    <a href="{{ route('admin.schedules.edit', $schedule) }}"
                       class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Edit</a>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-gray-400">
                <i class="fas fa-calendar text-3xl mb-2 opacity-30"></i>
                <p class="text-sm">Belum ada jadwal</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Sidebar Actions -->
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-900 mb-4">Aksi</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.fields.edit', $field) }}"
                   class="flex items-center gap-3 w-full bg-primary hover:bg-blue-800 text-white font-semibold px-4 py-3 rounded-xl text-sm transition">
                    <i class="fas fa-edit w-5 text-center"></i>Edit Lapangan
                </a>
                <a href="{{ route('admin.schedules.index') }}?field_id={{ $field->id_fields }}"
                   class="flex items-center gap-3 w-full bg-blue-50 hover:bg-blue-100 text-primary font-semibold px-4 py-3 rounded-xl text-sm transition">
                    <i class="fas fa-calendar w-5 text-center"></i>Lihat Semua Jadwal
                </a>
                <form method="POST" action="{{ route('admin.fields.destroy', $field) }}"
                      onsubmit="return confirm('Hapus lapangan ini secara permanen?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-3 w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold px-4 py-3 rounded-xl text-sm transition">
                        <i class="fas fa-trash w-5 text-center"></i>Hapus Lapangan
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Info Tambahan</h3>
            <div class="space-y-2.5 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span class="text-gray-400">Dibuat</span>
                    <span class="font-medium">{{ $field->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Diperbarui</span>
                    <span class="font-medium">{{ $field->updated_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
