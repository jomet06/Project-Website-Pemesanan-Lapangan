@extends('layouts.admin')

@section('title', 'Field Management - ActiveCourt')
@section('page-title', 'Field Management')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Manage Fields</h3>
        <button onclick="document.getElementById('addFieldModal').classList.remove('hidden')" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-1.5">
            <i class="fas fa-plus"></i>
            Add New Field
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50">
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Field Name</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Sport Type</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Location</th>
                    <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Sub-courts</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Price / Hour</th>
                    <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($fields ?? [] as $field)
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-200 rounded-lg overflow-hidden flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1544919982-b61976f0ba43?auto=format&fit=crop&q=80&w=100" class="w-full h-full object-cover">
                            </div>
                            <span class="font-semibold text-slate-800">{{ $field->name_fields }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-bold rounded-full">{{ $field->type_fields }}</span>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600">{{ $field->address ? Str::limit($field->address, 30) : '-' }}</td>
                    <td class="py-4 px-6 text-center">
                        <span class="text-sm font-semibold text-slate-700">{{ count($field->sub_courts ?? []) }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="font-semibold text-slate-700">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.fields.edit', $field->id_fields) }}" class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition flex items-center justify-center" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <form action="{{ route('admin.fields.destroy', $field->id_fields) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus lapangan {{ $field->name_fields }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition flex items-center justify-center" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <div class="text-5xl mb-3">🏟️</div>
                        <p class="text-slate-500 font-medium">Belum ada data lapangan</p>
                        <p class="text-slate-400 text-sm mt-1">Tambahkan lapangan baru untuk mulai mengelola.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Field Modal -->
<div id="addFieldModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Tambah Lapangan Baru</h3>
            <button onclick="document.getElementById('addFieldModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.fields.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lapangan</label>
                    <input type="text" name="name_fields" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipe Olahraga</label>
                    <select name="type_fields" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                        <option value="Futsal">Futsal</option>
                        <option value="Badminton">Badminton</option>
                        <option value="Basket">Basket</option>
                        <option value="Voli">Voli</option>
                        <option value="Tenis">Tenis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat</label>
                    <input type="text" name="address" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Harga per Jam (Rp)</label>
                    <input type="number" name="price_per_hour" required min="0"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kapasitas</label>
                    <input type="number" name="capacity" required min="1"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sub-Courts (JSON)</label>
                    <input type="text" name="sub_courts" value='["Lapangan 1"]' required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"
                           placeholder='["Lapangan 1", "Lapangan 2"]'>
                    <p class="text-xs text-slate-400 mt-1">Format JSON array</p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addFieldModal').classList.add('hidden')" class="bg-white border border-slate-200 text-slate-600 text-sm font-bold px-4 py-2.5 rounded-lg hover:bg-slate-50 transition">Batal</button>
                <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-4 py-2.5 rounded-lg transition shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
