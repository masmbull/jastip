@props([
    'brandName' => null,
    'brandOwner' => null,
    'brandTagline' => null,
])

@php
    $brandName = $brandName ?? setting('brand_name', 'NITIP DI END');
    $brandOwner = $brandOwner ?? setting('brand_owner', 'Nabila Adriyana');
    $brandTagline = $brandTagline ?? setting('brand_tagline', 'EH, NITIP DONG!');
@endphp

<section class="relative overflow-hidden py-20 md:py-32 bg-gradient-to-br from-[#FAF7F2] to-[#F0EDE7]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center max-w-4xl mx-auto">
            {{-- Headline --}}
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-[#2D2D2D] leading-tight mb-6">
                <span class="block">Mau Nitip?</span>
                <span class="text-rose-600">Nitip di End Aja!</span>
            </h1>

            {{-- Subheadline --}}
            <p class="text-lg md:text-xl text-[#666666] mb-8 max-w-2xl mx-auto">
                Jastip lokal pilihan <span class="font-semibold text-rose-500">{{ $brandOwner }}</span> —
                dari barang viral sampai kebutuhan favorit kamu.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-full shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-rose-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4a2 2 0 012-2m7-4V3a2 2 0 114 0v4m-4 0V3a2 2 0 114 0v4"></path>
                    </svg>
                    Lihat Barang
                </a>
                <a href="{{ route('cart.index') }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-[#2D2D2D] hover:bg-[#404040] text-white font-medium rounded-full shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#2D2D2D]/30">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4m-2 4h8M8 15l-2 4h8l-2-4"></path>
                    </svg>
                    Nitip Sekarang
                </a>
            </div>

            {{-- Product Visual --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="bg-white rounded-xl shadow-md p-4 text-center transform hover:-translate-y-1 transition-transform">
                    <div class="w-20 h-20 mx-auto bg-rose-100 rounded-full mb-2 flex items-center justify-center">
                        <span class="text-2xl">🧴</span>
                    </div>
                    <p class="text-sm font-medium text-[#666666]">Barang Viral</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 text-center transform hover:-translate-y-1 transition-transform">
                    <div class="w-20 h-20 mx-auto bg-rose-100 rounded-full mb-2 flex items-center justify-center">
                        <span class="text-2xl">🛍️</span>
                    </div>
                    <p class="text-sm font-medium text-[#666666]">Produk Lokal</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-4 text-center transform hover:-translate-y-1 transition-transform">
                    <div class="w-20 h-20 mx-auto bg-rose-100 rounded-full mb-2 flex items-center justify-center">
                        <span class="text-2xl">⚡</span>
                    </div>
                    <p class="text-sm font-medium text-[#666666]">Cepat Sampai</p>
                </div>
            </div>
        </div>

        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 -translate-y-1/4 w-64 h-64 bg-rose-100 rounded-full opacity-30 -z-10"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/4 w-48 h-48 bg-rose-100 rounded-full opacity-20 -z-10"></div>
    </div>
</section>