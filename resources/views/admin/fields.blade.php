@extends('layouts.admin')

@section('title', 'Field Management - ActiveCourt')
@section('page-title', 'Field Management')

@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Manage Fields</h3>
        <button class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-1.5">
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
                    <td class="py-4 px-6 text-sm text-slate-600">{{ $field->description ? Str::limit($field->description, 30) : '-' }}</td>
                    <td class="py-4 px-6 text-center">
                        <span class="text-sm font-semibold text-slate-700">3</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="font-semibold text-slate-700">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <button class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition flex items-center justify-center" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </button>
                            <button class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition flex items-center justify-center" title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
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
@endsection
