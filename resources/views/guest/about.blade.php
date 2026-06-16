@extends('layouts.app')
@section('title', 'Tentang Kami - ActiveCourt')
@section('content')

<!-- Hero -->
<section class="hero-bg py-20">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <span class="inline-block bg-blue-500 bg-opacity-20 text-blue-300 text-xs font-semibold px-3 py-1 rounded-full mb-4">Tentang Kami</span>
        <h1 class="text-4xl font-extrabold text-white mb-4">ActiveCourt</h1>
        <p class="text-blue-200 text-lg max-w-2xl mx-auto">Platform pemesanan lapangan olahraga berbasis website yang memudahkan Anda menemukan dan memesan lapangan futsal, basket, dan badminton premium.</p>
    </div>
</section>

<!-- About Content -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-20">
            <div>
                <span class="text-primary text-sm font-semibold uppercase tracking-wider">Tentang Proyek</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2 mb-5">Sistem Pemesanan Lapangan Olahraga Berbasis Website</h2>
                <p class="text-gray-600 leading-relaxed mb-4">ActiveCourt adalah sistem pemesanan lapangan olahraga yang dikembangkan sebagai proyek akhir perkuliahan. Platform ini dirancang untuk memudahkan pengguna menemukan dan memesan lapangan olahraga premium secara online.</p>
                <p class="text-gray-600 leading-relaxed mb-6">Dengan fitur real-time availability, pembayaran online melalui Midtrans, dan manajemen booking yang komprehensif, ActiveCourt hadir sebagai solusi modern untuk kebutuhan olahraga.</p>
                <div class="grid grid-cols-3 gap-4">
                    @foreach([['120+','Lapangan'],['5K+','Pengguna'],['3','Olahraga']] as $s)
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <p class="text-2xl font-bold text-primary">{{ $s[0] }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $s[1] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-gradient-to-br from-blue-900 to-blue-700 rounded-2xl p-10 text-white text-center">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-basketball text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2">Kelompok 10</h3>
                <p class="text-blue-200 mb-6">Proyek Akhir Pemrograman Web</p>
                <div class="space-y-2 text-sm text-left">
                    @foreach([
                        ['C14240003','Aldo Kurniawan Tanjung'],
                        ['C14240012','Vinsens Sandriawan'],
                        ['C14240017','Juliaan Matthew Wongsodjaja'],
                        ['C14240036','Hauw Feliciano Vincenzo Hawani'],
                        ['C14240060','Jonathan Matthew Suharyono'],
                    ] as $m)
                    <div class="flex gap-3 items-center bg-white bg-opacity-10 rounded-lg px-4 py-2.5">
                        <span class="bg-white bg-opacity-20 text-white text-xs font-mono px-2 py-0.5 rounded">{{ $m[0] }}</span>
                        <span class="text-white font-medium">{{ $m[1] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tech Stack -->
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Teknologi yang Digunakan</h2>
            <p class="text-gray-500">Stack modern untuk performa dan skalabilitas terbaik</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
            @foreach([
                ['fab fa-laravel','Laravel 11','PHP Framework','bg-red-50 text-red-600'],
                ['fas fa-database','MySQL','Database','bg-blue-50 text-blue-600'],
                ['fab fa-css3','Tailwind CSS','UI Framework','bg-sky-50 text-sky-600'],
                ['fas fa-credit-card','Midtrans','Payment Gateway','bg-green-50 text-green-600'],
            ] as $t)
            <div class="flex flex-col items-center p-6 {{ explode(' ',$t[3])[0] }} rounded-2xl border border-gray-100">
                <i class="{{ $t[0] }} text-3xl {{ explode(' ',$t[3])[1] }} mb-3"></i>
                <p class="font-bold text-gray-900">{{ $t[1] }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $t[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-10">Fitur Unggulan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['fas fa-user-shield','Autentikasi Multi-Role','Login via email/password atau Google OAuth. Role: Admin, User, dan Guest dengan akses berbeda.'],
                ['fas fa-calendar-check','Booking Real-time','Lihat jadwal tersedia secara langsung dan lakukan pemesanan dalam hitungan detik.'],
                ['fas fa-times-circle','Pembatalan Fleksibel','Batalkan booking maksimal H-3 sebelum jadwal bermain sesuai kebijakan lapangan.'],
                ['fas fa-credit-card','Pembayaran Online','Integrasi Midtrans untuk pembayaran aman melalui berbagai metode (transfer, e-wallet, dll).'],
                ['fas fa-envelope','Invoice Otomatis','Terima e-ticket dan invoice langsung ke email setelah pembayaran berhasil.'],
                ['fas fa-mobile-alt','REST API','API internal untuk data lapangan, jadwal, dan booking siap digunakan untuk pengembangan mobile app.'],
            ] as $f)
            <div class="bg-white rounded-2xl border border-gray-100 p-6 text-left hover:shadow-md transition">
                <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-xl flex items-center justify-center mb-4">
                    <i class="{{ $f[0] }} text-primary"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ $f[1] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $f[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection