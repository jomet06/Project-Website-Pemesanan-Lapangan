@extends('layouts.app')
@section('title', 'Tentang Kami - ActiveCourt')
@section('content')

{{-- Hero --}}
<section class="hero-bg py-24">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <span class="inline-block bg-blue-500 bg-opacity-20 text-blue-300 text-xs font-semibold px-4 py-1.5 rounded-full mb-5 tracking-wider uppercase">Tentang Kami</span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-5 leading-tight">
            Platform Pemesanan Lapangan<br>Olahraga Terpercaya
        </h1>
        <p class="text-blue-200 text-lg max-w-2xl mx-auto leading-relaxed">
            ActiveCourt hadir untuk memudahkan Anda menemukan, memesan, dan menikmati fasilitas olahraga premium — kapan saja, di mana saja.
        </p>
    </div>
</section>

{{-- Mission & Story --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-20">
            <div>
                <span class="text-primary text-sm font-semibold uppercase tracking-wider">Misi Kami</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2 mb-5">
                    Olahraga Jadi Lebih Mudah & Menyenangkan
                </h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Kami percaya bahwa akses ke fasilitas olahraga yang baik seharusnya mudah dan tanpa hambatan. ActiveCourt menghubungkan Anda dengan lapangan futsal, basket, dan badminton berkualitas tinggi melalui sistem pemesanan yang cepat dan transparan.
                </p>
                <p class="text-gray-600 leading-relaxed mb-8">
                    Dengan teknologi real-time availability dan pembayaran online yang aman, Anda tidak perlu lagi khawatir lapangan sudah dipesan atau proses bayar yang rumit.
                </p>
                <div class="grid grid-cols-3 gap-4">
                    @foreach([['6+','Lapangan Premium'],['3','Jenis Olahraga'],['24/7','Booking Online']] as $s)
                    <div class="text-center p-4 bg-gray-50 rounded-2xl">
                        <p class="text-2xl font-bold text-primary">{{ $s[0] }}</p>
                        <p class="text-gray-500 text-xs mt-1 font-medium">{{ $s[1] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-5">
                @foreach([
                    ['fas fa-bullseye','Fokus pada Pengguna','Setiap fitur dirancang untuk memberikan pengalaman booking yang semudah mungkin — dari memilih jadwal hingga konfirmasi pembayaran.'],
                    ['fas fa-shield-alt','Keamanan & Kepercayaan','Pembayaran diproses melalui gateway terpercaya. Data dan transaksi Anda selalu terlindungi.'],
                    ['fas fa-headset','Dukungan Responsif','Tim kami siap membantu jika ada kendala dengan pemesanan atau pertanyaan seputar lapangan.'],
                ] as $v)
                <div class="flex gap-4 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="w-11 h-11 bg-primary bg-opacity-10 rounded-xl flex items-center justify-center shrink-0">
                        <i class="{{ $v[0] }} text-primary"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">{{ $v[1] }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $v[2] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <span class="text-primary text-sm font-semibold uppercase tracking-wider">Fitur Platform</span>
        <h2 class="text-3xl font-bold text-gray-900 mt-2 mb-3">Semua yang Anda Butuhkan</h2>
        <p class="text-gray-500 mb-12 max-w-xl mx-auto">Dirancang agar pengalaman memesan lapangan terasa semudah berbelanja online.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['fas fa-calendar-check','Booking Real-time','Lihat jadwal tersedia secara langsung dan lakukan pemesanan dalam hitungan detik tanpa perlu telepon.'],
                ['fas fa-credit-card','Pembayaran Online Aman','Integrasi dengan payment gateway terpercaya. Bayar via transfer bank, kartu kredit, atau dompet digital.'],
                ['fas fa-times-circle','Kebijakan Pembatalan Jelas','Batalkan pemesanan hingga H-3 sebelum jadwal bermain — tanpa pertanyaan berlebihan.'],
                ['fas fa-user-shield','Akun & Riwayat Lengkap','Pantau semua riwayat booking, status pembayaran, dan detail lapangan dari satu dashboard.'],
                ['fas fa-mobile-alt','Akses dari Mana Saja','Tampilan responsif yang nyaman digunakan baik dari desktop maupun perangkat mobile.'],
                ['fas fa-map-marker-alt','Pilihan Lapangan Beragam','Futsal, basket, badminton — tersedia berbagai jenis lapangan dengan fasilitas lengkap.'],
            ] as $f)
            <div class="bg-white rounded-2xl border border-gray-100 p-6 text-left hover:shadow-md transition hover:-translate-y-1 duration-200">
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

{{-- CTA --}}
<section class="hero-bg py-16">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Siap Mulai Berolahraga?</h2>
        <p class="text-blue-200 mb-8">Daftar sekarang dan temukan lapangan terbaik di sekitar Anda.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('guest.fields') }}"
               class="bg-primary hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                Lihat Lapangan
            </a>
            <a href="{{ route('register') }}"
               class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-semibold px-8 py-3 rounded-xl border border-white border-opacity-30 transition">
                Daftar Gratis
            </a>
        </div>
    </div>
</section>

@endsection
