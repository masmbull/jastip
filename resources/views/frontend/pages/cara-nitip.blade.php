@extends('layouts.app')

@section('title', 'Cara Nitip | ' . setting('brand_name', 'NITIP DI END'))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">

    {{-- Header --}}
    <div class="text-center mb-14">
        <span class="inline-block px-4 py-1.5 bg-orange-100 dark:bg-orange-500/20 text-[#0E7490] dark:text-[#67e8f9] text-xs font-semibold uppercase tracking-wider rounded-full mb-4">
            Cara Kerja
        </span>
        <h1 class="text-3xl md:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-3">
            Nitip Itu Mudah
        </h1>
        <p class="text-[#64748B] dark:text-[#cbd5e1] max-w-lg mx-auto leading-relaxed">
            Tinggal 5 langkah simpel. Pilih barang, masukkan ke titipan, dan pesanan kamu segera diproses.
        </p>
    </div>

    {{-- Steps --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @php
            $steps = [
                ['icon' => 'eye', 'title' => 'Lihat Produk', 'desc' => 'Scroll dan cari barang yang kamu mau titip.'],
                ['icon' => 'plus', 'title' => 'Tambah ke Titipan', 'desc' => 'Klik "Tambah ke Titipan" atau "Nitip via WhatsApp".'],
                ['icon' => 'cart', 'title' => 'Isi Data', 'desc' => 'Masukkan nama, WhatsApp, alamat, dan catatan.'],
                ['icon' => 'check-circle', 'title' => 'Kirim ke Admin', 'desc' => 'Pesan WhatsApp otomatis dikirim ke admin untuk konfirmasi.'],
                ['icon' => 'truck', 'title' => 'Barang Sampai', 'desc' => 'Setelah dikonfirmasi, barang dikirim sesuai metode pengiriman Anda.'],
                ['icon' => 'cube', 'title' => 'Pesanan Selesai', 'desc' => 'Pesanan diterima dan ditindaklanjuti oleh admin.'],
            ];
        @endphp
        @foreach($steps as $step)
            <div class="nb-card p-5 text-center md:text-left">
                <x-icon :name="$step['icon']" class="w-9 h-9 mx-auto md:mx-0 mb-4 text-orange-500" />
                <span class="text-xs font-bold text-[#0E7490] dark:text-[#67e8f9] uppercase tracking-wider">Langkah {{ $loop->iteration }}</span>
                <h3 class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] text-lg mb-2 mt-1">{{ $step['title'] }}</h3>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] leading-relaxed">{{ $step['desc'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tips --}}
    <section class="mt-14 bg-[#F1F5F9] dark:bg-[#23252b] rounded-2xl p-6 md:p-8 border border-[#E2E8F0] dark:border-[#404854]">
        <div class="flex items-center gap-3 mb-5">
            <x-icon name="sparkles" class="w-7 h-7 text-orange-500" />
            <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Tips Lebih Cepat</h2>
        </div>
        <ul class="space-y-3 text-[#64748B] dark:text-[#cbd5e1]">
            <li class="flex items-start gap-3">
                <span class="text-orange-500 mt-0.5" aria-hidden="true">&bull;</span>
                <span>Pastikan nomor WhatsApp benar, admin akan membalas via WhatsApp.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-orange-500 mt-0.5" aria-hidden="true">&bull;</span>
                <span>Tulis catatan kalau ada preference warna / ukuran spesifik.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-orange-500 mt-0.5" aria-hidden="true">&bull;</span>
                <span>Pilih metode pengiriman yang sesuai, bisa COD atau kirim biasa.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-orange-500 mt-0.5" aria-hidden="true">&bull;</span>
                <span>Jangan ragu chat admin kalau ada pertanyaan sebelum order.</span>
            </li>
        </ul>
    </section>

    {{-- CTA --}}
    <div class="text-center mt-10">
        <x-btn as="a" :href="route('products.index')" variant="brand" icon="plus" class="rounded-full px-6 py-3">
            Mulai Nitip Sekarang
        </x-btn>
    </div>

</div>
@endsection