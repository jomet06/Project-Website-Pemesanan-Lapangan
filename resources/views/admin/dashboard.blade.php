@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional platform ActiveCourt')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @php
    $cards = [
        ['label'=>'Total Lapangan', 'value'=>$stats['total_fields'], 'icon'=>'fas fa-map-marker-alt', 'color'=>'blue'],
        ['label'=>'Total Pengguna', 'value'=>$stats['total_users'], 'icon'=>'fas fa-users', 'color'=>'indigo'],
        ['label'=>'Total Booking', 'value'=>$stats['total_bookings'], 'icon'=>'fas fa-clipboard-list', 'color'=>'violet'],
        ['label'=>'Menunggu', 'value'=>$stats['pending'], 'icon'=>'fas fa-hourglass-half', 'color'=>'amber'],
        ['label'=>'Dikonfirmasi', 'value'=>$stats['confirmed'], 'icon'=>'fas fa-check-circle', 'color'=>'green'],
        ['label'=>'Pendapatan', 'value'=>'Rp '.number_format($stats['revenue'],0,',','.'), 'icon'=>'fas fa-wallet', 'color'=>'emerald', 'small'=>true],
    ];
    $colorMap = [
        'blue'   => ['bg'=>'bg-blue-50',   'icon'=>'text-blue-600',   'val'=>'text-blue-700'],
        'indigo' => ['bg'=>'bg-indigo-50', 'icon'=>'text-indigo-600', 'val'=>'text-indigo-700'],
        'violet' => ['bg'=>'bg-violet-50', 'icon'=>'text-violet-600', 'val'=>'text-violet-700'],
        'amber'  => ['bg'=>'bg-amber-50',  'icon'=>'text-amber-600',  'val'=>'text-amber-700'],
        'green'  => ['bg'=>'bg-green-50',  'icon'=>'text-green-600',  'val'=>'text-green-700'],
        'emerald'=> ['bg'=>'bg-emerald-50','icon'=>'text-emerald-600','val'=>'text-emerald-700'],
    ];
    @endphp

    @foreach($cards as $card)
    @php $c = $colorMap[$card['color']]; @endphp
    <div class="stat-card p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="text-gray-500 text-xs font-medium">{{ $card['label'] }}</span>
            <div class="w-8 h-8 {{ $c['bg'] }} rounded-lg flex items-center justify-center">
                <i class="{{ $card['icon'] }} {{ $c['icon'] }} text-sm"></i>
            </div>
        </div>
        <p class="{{ isset($card['small']) ? 'text-lg' : 'text-2xl' }} font-bold {{ $c['val'] }}">
            {{ $card['value'] }}
        </p>
    </div>
    @endforeach
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('admin.fields.create') }}"
       class="flex items-center gap-3 bg-primary text-white rounded-xl px-4 py-3 hover:bg-primary-dark transition text-sm font-semibold">
        <i class="fas fa-plus-circle"></i> Tambah Lapangan
    </a>
    <a href="{{ route('admin.schedules.create') }}"
       class="flex items-center gap-3 bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 hover:bg-gray-50 transition text-sm font-semibold">
        <i class="fas fa-calendar-plus text-primary"></i> Buat Jadwal
    </a>
    <a href="{{ route('admin.bookings.index') }}"
       class="flex items-center gap-3 bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 hover:bg-gray-50 transition text-sm font-semibold">
        <i class="fas fa-clipboard-list text-violet-600"></i> Kelola Booking
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="flex items-center gap-3 bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 hover:bg-gray-50 transition text-sm font-semibold">
        <i class="fas fa-users text-indigo-600"></i> Kelola Pengguna
    </a>
</div>

{{-- Recent Bookings --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800 text-base">Booking Terbaru</h2>
        <a href="{{ route('admin.bookings.index') }}"
           class="text-primary text-sm font-medium hover:underline">Lihat Semua →</a>
    </div>

    @if($recentBookings->isEmpty())
        <div class="py-16 text-center text-gray-400">
            <i class="fas fa-inbox text-4xl mb-3"></i>
            <p class="text-sm">Belum ada booking.</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <th class="text-left px-6 py-3 font-semibold">Kode</th>
                    <th class="text-left px-6 py-3 font-semibold">Pengguna</th>
                    <th class="text-left px-6 py-3 font-semibold">Lapangan</th>
                    <th class="text-left px-6 py-3 font-semibold">Tanggal Main</th>
                    <th class="text-left px-6 py-3 font-semibold">Total</th>
                    <th class="text-left px-6 py-3 font-semibold">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentBookings as $booking)
                @php
                $statusMap = [
                    'pending'   => ['bg'=>'bg-amber-100 text-amber-700',  'label'=>'Menunggu'],
                    'confirmed' => ['bg'=>'bg-green-100 text-green-700',  'label'=>'Dikonfirmasi'],
                    'cancelled' => ['bg'=>'bg-red-100 text-red-700',      'label'=>'Dibatalkan'],
                ];
                $st = $statusMap[$booking->status_bookings] ?? ['bg'=>'bg-gray-100 text-gray-600','label'=>$booking->status_bookings];
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3.5">
                        <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                            {{ $booking->booking_code ?? '#'.$booking->id_bookings }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold shrink-0">
                                {{ strtoupper(substr($booking->user->name_users ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 text-sm truncate">{{ $booking->user->name_users ?? '-' }}</p>
                                <p class="text-gray-400 text-xs truncate">{{ $booking->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5">
                        <p class="font-medium text-gray-800">{{ $booking->schedule->field->name_fields ?? '-' }}</p>
                        <p class="text-gray-400 text-xs">{{ $booking->schedule->field->type_fields ?? '' }}</p>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">
                        {{ $booking->play_date ? \Carbon\Carbon::parse($booking->play_date)->format('d M Y') : '-' }}
                        @if($booking->schedule)
                        <p class="text-xs text-gray-400">
                            {{ substr($booking->schedule->start_time,0,5) }} – {{ substr($booking->schedule->end_time,0,5) }}
                        </p>
                        @endif
                    </td>
                    <td class="px-6 py-3.5 font-semibold text-gray-800">
                        Rp {{ number_format($booking->total_price,0,',','.') }}
                    </td>
                    <td class="px-6 py-3.5">
                        <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $st['bg'] }}">
                            {{ $st['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5">
                        <a href="{{ route('admin.bookings.show', $booking) }}"
                           class="text-primary text-xs font-medium hover:underline">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
