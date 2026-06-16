@extends('layouts.admin')

@section('title', 'Edit Field - ActiveCourt')
@section('page-title', 'Edit Field')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit Lapangan</h3>
        </div>

        <form action="{{ route('admin.fields.update', $field->id_fields) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lapangan</label>
                    <input type="text" name="name_fields" value="{{ old('name_fields', $field->name_fields) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipe Olahraga</label>
                    <select name="type_fields" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                        <option value="Futsal" {{ $field->type_fields == 'Futsal' ? 'selected' : '' }}>Futsal</option>
                        <option value="Badminton" {{ $field->type_fields == 'Badminton' ? 'selected' : '' }}>Badminton</option>
                        <option value="Basket" {{ $field->type_fields == 'Basket' ? 'selected' : '' }}>Basket</option>
                        <option value="Voli" {{ $field->type_fields == 'Voli' ? 'selected' : '' }}>Voli</option>
                        <option value="Tenis" {{ $field->type_fields == 'Tenis' ? 'selected' : '' }}>Tenis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat</label>
                    <input type="text" name="address" value="{{ old('address', $field->address) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Harga per Jam (Rp)</label>
                    <input type="number" name="price_per_hour" value="{{ old('price_per_hour', $field->price_per_hour) }}" required min="0"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kapasitas</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $field->capacity) }}" required min="1"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sub-Courts (JSON Array)</label>
                    <input type="text" name="sub_courts" value="{{ old('sub_courts', json_encode($field->sub_courts ?? ['Lapangan 1'])) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"
                           placeholder='["Lapangan 1", "Lapangan 2"]'>
                    <p class="text-xs text-slate-400 mt-1">Format JSON array, contoh: ["Lapangan 1","Lapangan 2"]</p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">{{ old('description', $field->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition shadow-sm">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.fields') }}" class="bg-white border border-slate-200 text-slate-600 text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-50 transition shadow-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
