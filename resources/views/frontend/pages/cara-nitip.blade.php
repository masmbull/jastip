@extends('layouts.app')

@section('title', 'Cara Nitip | ' . setting('brand_name', 'NITIP DI END'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    {{-- Header --}}
    <div class="text-center mb-14">
        <span class="inline-block px-4 py-1.5 bg-rose-100 text-rose-700 text-xs font-semibold uppercase tracking-wider rounded-full mb-4">
            Cara Kerja
        </span>
        <h1 class="text-3xl md:text-4xl font-bold text-[#2D2D2D] mb-3">
            Nitip Itu Mudah
        </h1>
        <p class="text-[#666666] max-w-lg mx-auto leading-relaxed">
            Tinggal 5 langkah simpel. Pilih barang, masukkan ke titipan, dan pesanan kamu segera diproses.
        </p>
    </div>

    {{-- Steps --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 counter">
        @php
            $steps = [
                ['icon' => '👀', 'title' => 'Lihat Produk', 'desc' => 'Scroll dan cari barang yang kamu mau titip.'],
                ['icon' => '➕', 'title' => 'Tambah ke Titipan', 'desc' => 'Klik "Tambah ke Titipan" atau "Nitip via WhatsApp".'],
                ['icon' => '🛒', 'title' => 'Isi Data', 'desc' => 'Masukkan nama, WhatsApp, alamat, dan catatan.'],
                ['icon' => '✅', 'title' => 'Kirim ke Admin', 'desc' => 'Pesan WhatsApp otomatis dikirim ke admin untuk konfirmasi.'],
                ['icon' => '🚚', 'title' => 'Barang Sampai', 'desc' => 'Setelah dikonfirmasi, barang dikirim sesuai metode pengiriman Anda.'],
                ['icon' => '📦', 'title' => 'Pesanan Selesai', 'desc' => 'Pesanan diterima dan ditindaklanjuti oleh admin.'],
            ];
        @endphp
        @foreach($steps as $step)
            <div class="bg-white rounded-xl shadow-sm p-6 text-center md:text-left border border-[#E8E0D8] hover:shadow-md transition-shadow">
                <div class="text-4xl mb-4">{{ $step['icon'] }}</div>
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider mb-1">Step {{ $loop->iteration }}</span>
                <h3 class="font-semibold text-[#2D2D2D] text-lg mb-2">{{ $step['title'] }}</h3>
                <p class="text-sm text-[#666666] leading-relaxed">{{ $step['desc'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tips --}}
    <section class="mt-14 bg-[#F8F5F0] rounded-2xl p-6 md:p-8">
        <div class="flex items-center gap-3 mb-5">
            <span class="text-2xl">💡</span>
            <h2 class="text-xl font-bold text-[#2D2D2D]">Tips Lebih Cepat</h2>
        </div>
        <ul class="space-y-3 text-[#666666]">
            <li class="flex items-start gap-3">
                <span class="text-rose-500 mt-0.5">•</span>
                <span>Pastikan nomor WhatsApp benar — admin akan membalas via WhatsApp.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-rose-500 mt-0.5">•</span>
                <span>Tulis catatan kalau ada preference warna / ukuran spesifik.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-rose-500 mt-0.5">•</span>
                <span>Pilih metode pengiriman yang sesuai — bisa COD atau kirim biasa.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-rose-500 mt-0.5">•</span>
                <span>Jangan ragu chat admin kalau ada pertanyaan sebelum order.</span>
            </li>
        </ul>
    </section>

    {{-- CTA --}}
    <div class="text-center mt-10">
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center px-6 py-3 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Mulai Nitip Sekarang
        </a>
    </div>

</div>
@endsection