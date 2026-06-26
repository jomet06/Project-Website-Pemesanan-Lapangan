@extends('layouts.admin')

@section('title', 'Edit Field - ActiveCourt')
@section('page-title', 'Edit Field')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit Lapangan</h3>
        </div>

        <form action="{{ route('admin.fields.update', $field->id_fields) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
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
                    @php
                        $currentType = old('type_fields', $field->type_fields);
                        $isStandard = in_array($currentType, ['Futsal', 'Badminton', 'Basket', 'Voli', 'Tenis']);
                        $selectedType = $isStandard ? $currentType : 'Lainnya';
                        $customType = $isStandard ? '' : $currentType;
                    @endphp
                    <div x-data="{ selectedType: '{{ $selectedType }}', customType: '{{ $customType }}' }">
                        <select x-model="selectedType" :name="selectedType === 'Lainnya' ? '' : 'type_fields'" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                            <option value="Futsal">Futsal</option>
                            <option value="Badminton">Badminton</option>
                            <option value="Basket">Basket</option>
                            <option value="Voli">Voli</option>
                            <option value="Tenis">Tenis</option>
                            <option value="Lainnya">Lainnya (Tulis Sendiri)</option>
                        </select>
                        <div x-show="selectedType === 'Lainnya'" style="{{ $selectedType === 'Lainnya' ? '' : 'display: none;' }}" class="mt-2">
                            <input type="text" x-model="customType" :name="selectedType === 'Lainnya' ? 'type_fields' : ''" :required="selectedType === 'Lainnya'" placeholder="Ketik tipe olahraga..." class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                        </div>
                    </div>
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
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sub-Courts (Pisahkan dengan Koma)</label>
                    <input type="text" name="sub_courts" value="{{ old('sub_courts', is_array($field->sub_courts) ? collect($field->sub_courts)->map(fn($c) => is_array($c) ? ($c['name'] ?? '') : $c)->implode(', ') : 'Lapangan 1') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"
                           placeholder="Contoh: Lapangan A, Lapangan B">
                    <p class="text-xs text-slate-400 mt-1">Pisahkan nama lapangan dengan tanda koma (,)</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Foto Lapangan (Opsional)</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-50 file:text-accent-700 hover:file:bg-accent-100">
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @if($field->image)
                        <div class="mt-2">
                            <p class="text-xs text-slate-500 mb-1">Foto saat ini:</p>
                            <img src="{{ asset('storage/' . $field->image) }}" alt="Foto Lapangan" class="h-20 w-auto rounded-md object-cover border border-slate-200">
                        </div>
                    @endif
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
