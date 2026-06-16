@extends('layouts.admin')
@section('title','Edit Lapangan')
@section('page-title','Edit Field')
@section('page-subtitle','Update sports field information.')
@section('content')

<div class="max-w-3xl">
    <a href="{{ route('admin.fields.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary mb-6 transition">
        <i class="fas fa-arrow-left"></i>Kembali ke Daftar Lapangan
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
        <form method="POST" action="{{ route('admin.fields.update', $field) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lapangan <span class="text-red-500">*</span></label>
                    <input type="text" name="name_fields" value="{{ old('name_fields', $field->name_fields) }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('name_fields')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori</label>
                    <input type="text" name="type_fields" value="{{ old('type_fields', $field->type_fields) }}"
                           placeholder="cth. Futsal, Badminton, Padel..."
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <p class="text-xs text-gray-400 mt-1">Isi bebas sesuai jenis olahraga lapangan ini.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kapasitas (orang) <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity" value="{{ old('capacity', $field->capacity) }}" min="1" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Harga per Jam (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-semibold">Rp</span>
                        <input type="number" name="price_per_hour" value="{{ old('price_per_hour', $field->price_per_hour) }}" min="0" step="1000" required
                               class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    @error('price_per_hour')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 pt-6">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           class="w-5 h-5 rounded accent-blue-600" {{ old('is_active', $field->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-semibold text-gray-700 cursor-pointer">Aktif (dapat dipesan)</label>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary resize-none">{{ old('description', $field->description) }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ganti Foto Lapangan</label>
                    @if($field->image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/'.$field->image) }}" class="w-40 h-32 object-cover rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-400 mt-1">Foto saat ini. Upload baru untuk mengganti.</p>
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*" id="imageInput" onchange="previewImage(event)"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none bg-white">
                    <img id="imagePreview" class="mt-3 w-40 h-32 object-cover rounded-xl hidden border border-gray-200">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas Tersedia</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($facilities as $facility)
                        <label class="flex items-center gap-2.5 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-primary transition">
                            <input type="checkbox" name="facilities[]" value="{{ $facility->id_facilities }}"
                                   class="accent-blue-600"
                                   {{ in_array($facility->id_facilities, old('facilities', $selectedFacilities)) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $facility->name_facilities }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.fields.index') }}"
                   class="flex-1 border border-gray-200 text-gray-700 hover:bg-gray-50 text-center font-semibold py-2.5 rounded-xl text-sm transition">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 bg-primary hover:bg-blue-800 text-white font-bold py-2.5 rounded-xl text-sm transition">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewImage(e) {
    const img = document.getElementById('imagePreview');
    img.src = URL.createObjectURL(e.target.files[0]);
    img.classList.remove('hidden');
}
</script>
@endpush
@endsection
