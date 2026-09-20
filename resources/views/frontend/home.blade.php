@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Hero --}}
    <x-hero />

    {{-- Category Section --}}
    <section class="py-12 md:py-16 animate-fade-up">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#333333] mb-3">Jelajahi Kategori</h2>
            <p class="text-[#999999]">Temukan produk berdasarkan kategori yang kamu inginkan.</p>
        </div>

        @if($categories->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <x-category-card :category="$category" />
                @endforeach
            </div>
        @else
            <p class="text-center text-[#999999] py-8">Belum ada kategori tersedia.</p>
        @endif
    </section>

    {{-- Featured Products --}}
    <section class="py-12 md:py-16 bg-white rounded-2xl shadow-sm animate-fade-up">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#333333] mb-3">Produk Unggulan</h2>
            <p class="text-[#999999]">Pilihan produk yang sedang ramai dibeli.</p>
        </div>

        @if($featuredProducts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <p class="text-center text-[#999999] py-8">Belum ada produk.</p>
        @endif

        <div class="text-center mt-10">
            <a href="{{ route('products.index') }}"
               class="inline-block px-8 py-3 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-rose-300">
                Lihat Semua Produk
            </a>
        </div>
    </section>

    {{-- Popular Products --}}
    <section class="py-12 md:py-16 animate-fade-up">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#333333] mb-3">Produk Populer</h2>
            <p class="text-[#999999]">Produk terlaris pilihan customers.</p>
        </div>

        @if($popularProducts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($popularProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <p class="text-center text-[#999999] py-8">Belum ada produk populer.</p>
        @endif
    </section>

    {{-- Call to Action --}}
    <section class="py-16 md:py-20 bg-gradient-to-r from-rose-500 to-pink-600 rounded-3xl shadow-xl text-center text-white mb-12 animate-fade-up">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Butuh Bantuan?</h2>
        <p class="text-lg md:text-xl text-rose-100 mb-6 max-w-2xl mx-auto">
            Ada pertanyaan? Hubungi admin kami via WhatsApp untuk bantuan cepat.
        </p>
        <a href="{{ setting('whatsapp', 'https://wa.me/6281234567890') }}"
           target="_blank"
           class="inline-flex items-center justify-center px-8 py-4 bg-white text-rose-600 font-bold rounded-full shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 18l9-5-9-5-9 5 9 5z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 12v6m0 0l-3-3m3 3l3-3"></path>
            </svg>
            Chat via WhatsApp
        </a>
    </section>

</div>
@endsection