@extends('layouts.admin')

@section('title', 'Dashboard Admin - ActiveCourt')
@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-500">Total Revenue</span>
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">Rp {{ number_format($totalRevenue ?? 42500000, 0, ',', '.') }}</p>
        <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> +12.5% dari bulan lalu
        </p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-500">Active Bookings</span>
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">{{ $activeBookings ?? 24 }}</p>
        <p class="text-xs text-blue-600 mt-1 flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> +3 dari minggu lalu
        </p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-500">New Users (Monthly)</span>
            <div class="w-10 h-10 bg-accent-100 text-accent-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">{{ $newUsers ?? 128 }}</p>
        <p class="text-xs text-accent-600 mt-1 flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> +18% dari bulan lalu
        </p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-500">Conversion Rate</span>
            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">{{ $conversionRate ?? 68.5 }}%</p>
        <p class="text-xs text-purple-600 mt-1 flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> +2.1% dari bulan lalu
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Bookings -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Recent Bookings</h3>
            <a href="{{ route('admin.bookings') }}" class="text-sm text-accent-600 hover:text-accent-700 font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">Booking ID</th>
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">Field</th>
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">User</th>
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentBookings ?? [] as $booking)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-6 text-sm font-semibold text-primary-700">{{ $booking->booking_code }}</td>
                        <td class="py-3 px-6 text-sm text-slate-600">{{ $booking->schedule->field->name_fields ?? '-' }}</td>
                        <td class="py-3 px-6 text-sm text-slate-600">{{ $booking->user->name_users ?? '-' }}</td>
                        <td class="py-3 px-6">
                            @if($booking->status_bookings === 'Paid')
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Paid</span>
                            @elseif($booking->status_bookings === 'Pending')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Pending</span>
                            @else
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Cancelled</span>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-sm font-semibold text-slate-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Belum ada data booking</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Quick Stats</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-600">Total Lapangan</span>
                <span class="font-bold text-slate-800">{{ $totalFields ?? 12 }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-600">Total Pengguna</span>
                <span class="font-bold text-slate-800">{{ $totalUsers ?? 256 }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-600">Booking Hari Ini</span>
                <span class="font-bold text-slate-800">{{ $todayBookings ?? 5 }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-600">Pendapatan Hari Ini</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($todayRevenue ?? 750000, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-sm text-slate-600">Booking Pending</span>
                <span class="font-bold text-amber-600">{{ $pendingBookings ?? 3 }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
