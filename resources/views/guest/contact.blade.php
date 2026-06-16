@extends('layouts.app')
@section('title', 'Kontak - ActiveCourt')
@section('content')

<section class="hero-bg py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-extrabold text-white mb-3">Hubungi Kami</h1>
        <p class="text-blue-200 text-lg">Ada pertanyaan? Tim kami siap membantu Anda</p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Info -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Info Kontak</h2>
                <div class="space-y-5 mb-10">
                    @foreach([
                        ['fas fa-map-marker-alt','Alamat','Universitas Ciputra Surabaya, Citraland CBD Boulevard, Surabaya, Jawa Timur'],
                        ['fas fa-envelope','Email','kelompok10@activecourt.id'],
                        ['fas fa-phone','Telepon','+62 31 7451111'],
                        ['fas fa-clock','Jam Layanan','Senin – Jumat, 08.00 – 17.00 WIB'],
                    ] as $c)
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-primary bg-opacity-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="{{ $c[0] }} text-primary text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $c[1] }}</p>
                            <p class="text-gray-500 text-sm mt-0.5">{{ $c[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <h3 class="font-bold text-gray-900 mb-4">Tim Pengembang</h3>
                <div class="space-y-2">
                    @foreach([
                        ['Aldo Kurniawan Tanjung','C14240003'],
                        ['Vinsens Sandriawan','C14240012'],
                        ['Juliaan Matthew Wongsodjaja','C14240017'],
                        ['Hauw Feliciano Vincenzo Hawani','C14240036'],
                        ['Jonathan Matthew Suharyono','C14240060'],
                    ] as $m)
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-sm font-medium text-gray-800">{{ $m[0] }}</span>
                        <span class="text-xs text-gray-500 font-mono">{{ $m[1] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Form -->
            <div class="bg-gray-50 rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Kirim Pesan</h2>
                @if(session('contact_success'))
                    <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
                        <i class="fas fa-check-circle mr-2 text-green-500"></i>Pesan berhasil dikirim! Kami akan menghubungi Anda segera.
                    </div>
                @endif
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" placeholder="Nama Anda"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" placeholder="email@anda.com"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Subjek</label>
                        <input type="text" placeholder="Subjek pesan"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan</label>
                        <textarea rows="5" placeholder="Tulis pesan Anda di sini..."
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary resize-none"></textarea>
                    </div>
                    <button class="w-full bg-primary hover:bg-blue-800 text-white py-3 rounded-xl font-semibold text-sm transition">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection