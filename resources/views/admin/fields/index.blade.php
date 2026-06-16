@extends('layouts.admin')
@section('title','Field Management')
@section('page-title','Field Management')
@section('page-subtitle','Configure and manage your sports facility assets.')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <select onchange="this.form.submit()" form="filterForm" name="type"
                class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary bg-white">
            <option value="">All Sports</option>
            @foreach(\App\Models\Field::distinct()->pluck('type_fields')->filter() as $t)
                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <form id="filterForm" method="GET" action="{{ route('admin.fields.index') }}" class="hidden"></form>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500">Showing {{ $fields->total() }} of {{ $fields->total() }} fields</span>
        <a href="{{ route('admin.fields.create') }}"
           class="flex items-center gap-2 bg-primary hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded-xl text-sm transition">
            <i class="fas fa-plus"></i>Add New Field
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Field Name</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sport Type</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Capacity</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Price / Hour</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($fields as $field)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        @if($field->image)
                            <img src="{{ asset('storage/'.$field->image) }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-futbol text-primary text-sm"></i>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 text-sm leading-none">{{ $field->name_fields }}</p>
                            @if($field->is_active)
                                <span class="text-xs text-green-600 font-semibold mt-0.5 inline-block">Available</span>
                            @else
                                <span class="text-xs text-red-500 font-semibold mt-0.5 inline-block">Inactive</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                        <i class="fas fa-futbol text-gray-400 text-xs"></i>{{ $field->type_fields ?? '-' }}
                    </span>
                </td>
                <td class="px-5 py-4 text-sm text-gray-700">{{ $field->capacity }} orang</td>
                <td class="px-5 py-4">
                    <span class="font-bold text-primary text-sm">Rp {{ number_format($field->price_per_hour,0,',','.') }}</span>
                </td>
                <td class="px-5 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $field->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $field->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.fields.show', $field) }}"
                           class="text-xs font-semibold text-gray-600 hover:text-primary bg-gray-100 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.fields.edit', $field) }}"
                           class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('admin.fields.destroy', $field) }}"
                              onsubmit="return confirm('Hapus lapangan ini? Semua jadwal terkait juga akan dihapus.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">
                <i class="fas fa-futbol text-4xl mb-3 opacity-30"></i>
                <p>Belum ada lapangan. <a href="{{ route('admin.fields.create') }}" class="text-primary font-semibold">Tambah sekarang</a></p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($fields->hasPages())
<div class="mt-5 flex justify-center">{{ $fields->links() }}</div>
@endif
@endsection
