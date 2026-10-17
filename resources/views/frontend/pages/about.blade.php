@extends('layouts.app')
@section('title', 'Tentang Kami')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">

    @php $brandName = setting('brand_name', 'NITIP DI END'); $brandOwner = setting('brand_owner', 'Nabila Adriyana'); $brandTagline = setting('brand_tagline', 'EH, NITIP DONG!'); @endphp

    <div class="text-center mb-12 sm:mb-16">
        <h1 class="text-3xl sm:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Tentang {{ $brandName }}</h1>
        <p class="text-lg sm:text-xl text-[#141414] dark:text-[#FAF6EF] dark:text-[#141414] dark:text-[#FAF6EF] font-medium">{{ $brandTagline }}</p>
        <div class="mt-4 flex items-center justify-center gap-2 text-sm text-[#64748B] dark:text-[#cbd5e1]">
            <span>by</span><span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $brandOwner }}</span>
        </div>
    </div>

    <div class="max-w-3xl mx-auto mb-16">
        <x-card>
            <h2 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Cerita di Balik Brand</h2>
            <p class="text-[#64748B] dark:text-[#cbd5e1] leading-relaxed mb-4">
                {{ $brandName }} adalah bisnis jastip lokal yang berawal dari hasrat sederhana:
                ingin memudahkan orang menemukan dan mendapatkan barang-barang yang mereka incari.
            </p>
            <p class="text-[#64748B] dark:text-[#cbd5e1] leading-relaxed mb-4">
                Kami memfokuskan diri pada produk-produk lokal Indonesia yang viral dan berkualitas,
                dari parfum, tumbler, fashion, lifestyle products, hingga barang-barang trending lainnya.
            </p>
            <p class="text-[#64748B] dark:text-[#cbd5e1] leading-relaxed">
                Dengan layanan yang responsif dan proses yang mudah, kami berharap setiap customer
                bisa menemukan barang yang mereka inginkan dengan nyaman dan tanpa ribet.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div class="bg-[#F8FAFC] dark:bg-[#2e323b] rounded-lg p-5 border border-[#E2E8F0] dark:border-[#404854]">
                    <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-2 flex items-center gap-2"><x-icon name="sparkles" class="w-5 h-5" /> Visi</h3>
                    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] leading-relaxed">Menjadi layanan jastip lokal paling terpercaya di Indonesia, tempat kamu bisa dapat produk impian dengan harga terbaik, proses mudah, dan garansi autentik 100%.</p>
                </div>
                <div class="bg-[#F8FAFC] dark:bg-[#2e323b] rounded-lg p-5 border border-[#E2E8F0] dark:border-[#404854]">
                    <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-2 flex items-center gap-2"><x-icon name="bolt" class="w-5 h-5" /> Misi</h3>
                    <ul class="text-sm text-[#64748B] dark:text-[#cbd5e1] leading-relaxed list-disc pl-4 space-y-1">
                        <li>Memudahkan siapa pun menitip barang viral & original.</li>
                        <li>Transparan soal harga, ongkir, dan status pesanan.</li>
                        <li>Respons cepat via WhatsApp & garansi uang kembali.</li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-8">
                <x-btn as="a" :href="route('contact')" variant="outline" icon="envelope" class="rounded-full px-6 py-3">
                    Hubungi Kami
                </x-btn>
            </div>
        </x-card>
    </div>

    {{-- Nilai Brand --}}
    <div class="max-w-4xl mx-auto mb-16">
        <h2 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-8 text-center">Nilai yang Kami Pegang</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-card class="text-center">
                <div class="w-14 h-14 bg-orange-100 dark:bg-orange-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="lock" class="w-7 h-7 text-[#141414] dark:text-[#FAF6EF] dark:text-[#141414] dark:text-[#FAF6EF]" />
                </div>
                <h3 class="font-bold text-lg text-[#1E293B] dark:text-[#f1f5f9] mb-2">Privasi Terjaga</h3>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Setiap data pelanggan kami jaga dengan baik. Tidak ada info yang bocor atau dijual ke pihak manapun.</p>
            </x-card>
            <x-card class="text-center">
                <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="check-circle" class="w-7 h-7 text-emerald-600 dark:text-emerald-300" />
                </div>
                <h3 class="font-bold text-lg text-[#1E293B] dark:text-[#f1f5f9] mb-2">Original &amp; Viral</h3>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Kami hanya jual produk original dan yang lagi trending. Barang viral langsung kita ambil, langsung bisa didapat.</p>
            </x-card>
            <x-card class="text-center">
                <div class="w-14 h-14 bg-cyan-100 dark:bg-cyan-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="bolt" class="w-7 h-7 text-[#141414] dark:text-[#FAF6EF] dark:text-[#141414] dark:text-[#FAF6EF]" />
                </div>
                <h3 class="font-bold text-lg text-[#1E293B] dark:text-[#f1f5f9] mb-2">Cepat &amp; Mudah</h3>
                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Cukup ketik di WhatsApp, transfer, dan barang sampai. Proses jastip kita runcing biar nggak lama.</p>
            </x-card>
        </div>
    </div>

    {{-- Kerjasama Brand --}}
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Ingin Jadi Mitra Kerja Sama?</h2>
        <p class="text-[#64748B] dark:text-[#cbd5e1] mb-6 max-w-2xl mx-auto">
            Apakah kamu punya toko, brand, atau usaha yang ingin diajukan untuk jadi partner jastip?
            Kami terbuka untuk kerjasama yang saling menguntungkan.
        </p>
        <x-btn as="a" :href="route('contact')" variant="brand" icon="chat" class="rounded-full px-6 py-3">
            Hubungi Kami
        </x-btn>
    </div>

</div>
@endsection