@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Hero --}}
    <x-hero />

    {{-- Category Section --}}
    <section class="py-12 md:py-16 animate-fade-up">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#1E293B] mb-3">Jelajahi Kategori</h2>
            <p class="text-[#94A3B8]">Temukan produk berdasarkan kategori yang kamu inginkan.</p>
        </div>

        @if($categories->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <x-category-card :category="$category" />
                @endforeach
            </div>
        @else
            <p class="text-center text-[#94A3B8] py-8">Belum ada kategori tersedia.</p>
        @endif
    </section>

    {{-- Featured Products --}}
    <section class="py-12 md:py-16 bg-white rounded-2xl shadow-sm animate-fade-up">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#1E293B] mb-3">Produk Unggulan</h2>
            <p class="text-[#94A3B8]">Pilihan produk yang sedang ramai dibeli.</p>
        </div>

        @if($featuredProducts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <p class="text-center text-[#94A3B8] py-8">Belum ada produk.</p>
        @endif

        <div class="text-center mt-10">
            <a href="{{ route('products.index') }}"
               class="inline-block px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                Lihat Semua Produk
            </a>
        </div>
    </section>

    {{-- Popular Products --}}
    <section class="py-12 md:py-16 animate-fade-up">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#1E293B] mb-3">Produk Populer</h2>
            <p class="text-[#94A3B8]">Produk terlaris pilihan customers.</p>
        </div>

        @if($popularProducts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($popularProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <p class="text-center text-[#94A3B8] py-8">Belum ada produk populer.</p>
        @endif
    </section>

    {{-- Produk Viral --}}
    @if(isset($viralProducts) && $viralProducts->isNotEmpty())
        <section class="py-12 md:py-16 animate-fade-up">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold uppercase tracking-wide">
                        🔥 Trending 2024–2025
                    </span>
                    <h2 class="mt-3 text-3xl font-bold text-[#1E293B]">Produk Viral</h2>
                    <p class="mt-2 text-[#94A3B8]">Produk yang sedang ramai diburu di TikTok Shop &amp; Shopee Indonesia.</p>
                </div>
                <a href="{{ route('products.viral') }}"
                   class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Lihat Semua
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($viralProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Call to Action --}}
    <section class="py-16 md:py-20 bg-gradient-to-r from-orange-500 to-orange-600 rounded-3xl shadow-xl text-center text-white mb-12 animate-fade-up">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Butuh Bantuan?</h2>
        <p class="text-lg md:text-xl text-orange-100 mb-6 max-w-2xl mx-auto">
            Ada pertanyaan? Hubungi admin kami via WhatsApp untuk bantuan cepat.
        </p>
        <a href="{{ \App\Services\WhatsappService::contactUrl('Halo Kak, saya mau tanya soal nitipan di ' . setting('brand_name', 'NITIP DI END')) }}"
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center px-8 py-4 bg-white text-orange-600 font-bold rounded-full shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z"></path>
            </svg>
            Chat Admin
        </a>
    </section>

</div>
@endsection