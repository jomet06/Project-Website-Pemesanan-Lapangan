@extends('layouts.admin')
@section('title','Tambah Jadwal')
@section('page-title','Tambah Jadwal')
@section('page-subtitle','Buat slot waktu baru untuk lapangan.')
@section('content')

<div class="max-w-xl">
    <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary mb-6 transition">
        <i class="fas fa-arrow-left"></i>Kembali ke Daftar Jadwal
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
        <form method="POST" action="{{ route('admin.schedules.store') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lapangan <span class="text-red-500">*</span></label>
                    <select name="field_id" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                        <option value="">-- Pilih Lapangan --</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id_fields }}"
                                {{ old('field_id', request('field_id')) == $field->id_fields ? 'selected' : '' }}>
                                {{ $field->name_fields }} ({{ $field->type_fields }})
                            </option>
                        @endforeach
                    </select>
                    @error('field_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                           min="{{ date('Y-m-d') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('start_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" name="end_time" value="{{ old('end_time', '09:00') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('end_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                @error('time')<p class="text-red-500 text-xs -mt-3">{{ $message }}</p>@enderror

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <select name="status_schedules"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                        <option value="available" {{ old('status_schedules') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="closed" {{ old('status_schedules') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-6 border-t border-gray-100 mt-6">
                <a href="{{ route('admin.schedules.index') }}"
                   class="flex-1 border border-gray-200 text-gray-700 hover:bg-gray-50 text-center font-semibold py-2.5 rounded-xl text-sm transition">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 bg-primary hover:bg-blue-800 text-white font-bold py-2.5 rounded-xl text-sm transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
