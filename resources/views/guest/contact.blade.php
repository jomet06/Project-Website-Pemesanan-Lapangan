@extends('layouts.app')
@section('title', 'Kontak - ActiveCourt')
@section('content')

{{-- Hero --}}
<section class="hero-bg py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <span class="inline-block bg-blue-500 bg-opacity-20 text-blue-300 text-xs font-semibold px-4 py-1.5 rounded-full mb-5 uppercase tracking-wider">Kontak</span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4">Ada Pertanyaan?</h1>
        <p class="text-blue-200 text-lg">Tim kami siap membantu Anda — baik soal pemesanan, lapangan, maupun masalah teknis.</p>
    </div>
</section>

{{-- Content --}}
<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            {{-- Contact Info --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Info Kontak</h2>
                <p class="text-gray-500 text-sm mb-8">Kami berusaha merespons setiap pesan dalam 1×24 jam di hari kerja.</p>

                <div class="space-y-5 mb-10">
                    @foreach([
                        ['fas fa-envelope','Email','support@activecourt.id'],
                        ['fas fa-phone','WhatsApp','+62 812-3456-7890'],
                        ['fas fa-clock','Jam Layanan','Senin – Jumat, 08.00 – 17.00 WIB'],
                        ['fas fa-map-marker-alt','Operasional','Surabaya, Jawa Timur'],
                    ] as $c)
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-primary bg-opacity-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                            <i class="{{ $c[0] }} text-primary text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $c[1] }}</p>
                            <p class="text-gray-500 text-sm mt-0.5">{{ $c[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- FAQ Quick Links --}}
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-question-circle text-primary"></i> Pertanyaan Umum
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-chevron-right text-primary text-xs mt-1 shrink-0"></i>
                            Bagaimana cara memesan lapangan?
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-chevron-right text-primary text-xs mt-1 shrink-0"></i>
                            Metode pembayaran apa saja yang tersedia?
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-chevron-right text-primary text-xs mt-1 shrink-0"></i>
                            Bagaimana kebijakan pembatalan booking?
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-chevron-right text-primary text-xs mt-1 shrink-0"></i>
                            Apakah booking bisa diubah jadwalnya?
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Kirim Pesan</h2>
                <p class="text-gray-500 text-sm mb-6">Isi formulir di bawah dan kami akan segera menghubungi Anda.</p>

                @if(session('contact_success'))
                    <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Pesan berhasil dikirim! Kami akan menghubungi Anda segera.
                    </div>
                @endif

                <form class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" placeholder="Nama Anda"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor HP</label>
                            <input type="tel" placeholder="+62 8xx-xxxx-xxxx"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" placeholder="email@anda.com"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Topik</label>
                        <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary text-gray-700">
                            <option value="">Pilih topik...</option>
                            <option>Pertanyaan Booking</option>
                            <option>Masalah Pembayaran</option>
                            <option>Laporan Masalah Teknis</option>
                            <option>Kerja Sama / Mitra</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan</label>
                        <textarea rows="5" placeholder="Jelaskan pertanyaan atau kendala Anda..."
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary resize-none"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full bg-primary hover:bg-blue-800 text-white py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
