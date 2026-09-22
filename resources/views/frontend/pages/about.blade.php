@extends('layouts.app')
@section('title', 'Tentang Kami')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    @php $brandName = setting('brand_name', 'NITIP DI END'); $brandOwner = setting('brand_owner', 'Nabila Adriyana'); $brandTagline = setting('brand_tagline', 'EH, NITIP DONG!'); @endphp

    <div class="text-center mb-16">
        <h1 class="text-4xl font-bold text-[#1E293B] mb-4">Tentang {{ $brandName }}</h1>
        <p class="text-xl text-orange-600 font-medium">{{ $brandTagline }}</p>
        <div class="mt-4 flex items-center justify-center gap-2 text-sm text-[#94A3B8]">
            <span>by</span><span class="font-medium text-[#1E293B]">{{ $brandOwner }}</span>
        </div>
    </div>

    <div class="max-w-3xl mx-auto mb-16">
        <div class="bg-white rounded-xl shadow-sm p-8 border border-[#E2E8F0]">
            <h2 class="text-2xl font-bold text-[#1E293B] mb-4">Cerita di Balik Brand</h2>
            <p class="text-[#64748B] leading-relaxed mb-4">
                {{ $brandName }} adalah bisnis jastip lokal yang berawal dari hasrat sederhana:
                ingin memudahkan orang menemukan dan mendapatkan barang-barang yang mereka incari.
            </p>
            <p class="text-[#64748B] leading-relaxed mb-4">
                Kami memfokuskan diri pada produk-produk lokal Indonesia yang viral dan berkualitas —
                dari parfum, tumbler, fashion, lifestyle products, hingga barang-barang trending lainnya.
            </p>
            <p class="text-[#64748B] leading-relaxed">
                Dengan layanan yang responsif dan proses yang mudah, kami berharap setiap customer
                bisa menemukan barang yang mereka inginkan dengan nyaman dan tanpa ribet.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div class="bg-[#FDF6EC] rounded-lg p-5 border border-orange-100">
                    <h3 class="font-bold text-[#1E293B] mb-2 flex items-center gap-2">🎯 Visi</h3>
                    <p class="text-sm text-[#64748B] leading-relaxed">Menjadi layanan jastip lokal paling terpercaya di Indonesia — tempat kamu bisa dapat produk impian dengan harga terbaik, proses mudah, dan garansi autentik 100%.</p>
                </div>
                <div class="bg-[#FDF6EC] rounded-lg p-5 border border-orange-100">
                    <h3 class="font-bold text-[#1E293B] mb-2 flex items-center gap-2">🚀 Misi</h3>
                    <ul class="text-sm text-[#64748B] leading-relaxed list-disc pl-4 space-y-1">
                        <li>Memudahkan siapa pun menitip barang viral & original.</li>
                        <li>Transparan soal harga, ongkir, dan status pesanan.</li>
                        <li>Respons cepat via WhatsApp & garansi uang kembali.</li>
                    </ul>
                </div>
            </div>
    <div class="text-center">
        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-orange-500 text-orange-600 font-medium rounded-full hover:bg-orange-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            Hubungi Kami
        </a>
    </div>
</div>
</div>

{{-- Nilai Brand --}}
<div class="max-w-4xl mx-auto mb-16">
    <h2 class="text-2xl font-bold text-[#1E293B] mb-8 text-center">Nilai yang Kami Pegang</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-[#E2E8F0] text-center">
            <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h3 class="font-bold text-lg text-[#1E293B] mb-2">Privasi Terjaga</h3>
            <p class="text-sm text-[#64748B]">Setiap data pelanggan kami jaga dengan baik. Tidak ada info yang bocor atau dijual ke pihak manapun.</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-[#E2E8F0] text-center">
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="font-bold text-lg text-[#1E293B] mb-2">Original &amp; Viral</h3>
            <p class="text-sm text-[#64748B]">Kami hanya jual produk original dan yang lagi trending. Barang viral langsung kita ambil, langsung bisa didapat.</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-[#E2E8F0] text-center">
            <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h3 class="font-bold text-lg text-[#1E293B] mb-2">Cepat &amp; Mudah</h3>
            <p class="text-sm text-[#64748B]">Cukup ketik di WhatsApp, transfer, dan barang sampai. Proses jastip kita runcing biar nggak lama.</p>
        </div>
    </div>
</div>

{{-- Kerjasama Brand --}}
<div class="max-w-4xl mx-auto text-center">
    <h2 class="text-2xl font-bold text-[#1E293B] mb-6">Ingin Jadi Mitra Kerja Sama?</h2>
    <p class="text-[#64748B] mb-6 max-w-2xl mx-auto">
        Apakah kamu punya toko, brand, atau usaha yang ingin diajukan untuk jadi partner jastip?
        Kami terbuka untuk kerjasama yang saling menguntungkan.
    </p>
    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1E293B] hover:bg-[#444444] text-white font-medium rounded-full transition-colors shadow-md">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        Hubungi Kami
    </a>
</div>

</div>
@endsection