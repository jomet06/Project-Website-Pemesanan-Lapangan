@extends('layouts.app')

@section('title', 'Beranda - SportBooking')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-emerald-700 text-white overflow-hidden">
        <div class="absolute inset-0">
            <!-- Background pattern/image overlay -->
            <img src="https://images.unsplash.com/photo-1518605368461-1e1252220a22?auto=format&fit=crop&q=80&w=2000" alt="Background" class="w-full h-full object-cover opacity-20" />
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-800 to-transparent"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                    Booking Lapangan Olahraga Jadi Lebih Mudah
                </h1>
                <p class="text-lg md:text-xl text-emerald-100 mb-8">
                    Temukan dan pesan lapangan futsal, basket, atau badminton favoritmu dalam hitungan menit. Cek jadwal real-time dan bayar dengan aman.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#" class="bg-white text-emerald-700 hover:bg-emerald-50 px-8 py-3 rounded-lg font-bold text-center shadow-lg transition">
                        Cari Lapangan
                    </a>
                    <a href="#" class="bg-emerald-600 border border-emerald-500 hover:bg-emerald-500 text-white px-8 py-3 rounded-lg font-bold text-center transition">
                        Cara Pesan
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Keunggulan (Features) Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-800">Kenapa Memilih Kami?</h2>
                <p class="mt-4 text-slate-600">Kami memberikan pengalaman terbaik untuk aktivitas olahraga Anda.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-6 border border-slate-100 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Jadwal Real-Time</h3>
                    <p class="text-slate-600">Lihat ketersediaan lapangan secara langsung. Tidak ada lagi miskomunikasi atau double-booking.</p>
                </div>
                <!-- Feature 2 -->
                <div class="text-center p-6 border border-slate-100 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Pembayaran Mudah</h3>
                    <p class="text-slate-600">Integrasi dengan Midtrans. Bayar via transfer bank, e-wallet, atau kartu kredit dengan aman.</p>
                </div>
                <!-- Feature 3 -->
                <div class="text-center p-6 border border-slate-100 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Batal Booking H-3</h3>
                    <p class="text-slate-600">Ada halangan mendadak? Batalkan pesananmu maksimal 3 hari sebelum jadwal main.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Lapangan Populer Section -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800">Lapangan Tersedia</h2>
                    <p class="mt-2 text-slate-600">Pilih lapangan sesuai dengan olahraga favoritmu.</p>
                </div>
                <a href="#" class="hidden sm:inline-flex text-emerald-600 font-semibold hover:text-emerald-700">Lihat Semua &rarr;</a>
            </div>

            <!-- Grid Lapangan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                @forelse($fields as $field)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition group">
                        <div class="relative h-48 bg-slate-200 overflow-hidden">
                            <!-- Jika belum ada gambar asli, pakai dummy Unsplash -->
                            <img src="https://images.unsplash.com/photo-1544919982-b61976f0ba43?auto=format&fit=crop&q=80&w=800" alt="{{ $field->name_fields }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-emerald-700">
                                {{ $field->type_fields }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-2">{{ $field->name_fields }}</h3>
                            <p class="text-slate-500 text-sm mb-4 line-clamp-2">{{ $field->description }}</p>
                            
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-100">
                                <div>
                                    <span class="text-xs text-slate-500 block">Mulai dari</span>
                                    <span class="text-lg font-extrabold text-emerald-600">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}<span class="text-sm font-normal text-slate-500">/jam</span></span>
                                </div>
                                <a href="#" class="bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                                    Booking
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-slate-500">Belum ada data lapangan tersedia saat ini.</p>
                    </div>
                @endforelse

            </div>
            
            <div class="mt-8 text-center sm:hidden">
                <a href="#" class="inline-flex text-emerald-600 font-semibold hover:text-emerald-700">Lihat Semua Lapangan &rarr;</a>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-20 bg-emerald-600 text-white text-center px-4">
        <h2 class="text-3xl font-bold mb-4">Siap untuk Bertanding?</h2>
        <p class="mb-8 text-emerald-100 max-w-xl mx-auto">Jangan sampai kehabisan slot. Buat akun sekarang dan nikmati kemudahan booking lapangan hanya dari smartphone Anda.</p>
        <a href="#" class="bg-white text-emerald-700 hover:bg-emerald-50 px-8 py-3 rounded-lg font-bold text-lg inline-block shadow-lg transition">Daftar Sekarang - Gratis</a>
    </section>
@endsection