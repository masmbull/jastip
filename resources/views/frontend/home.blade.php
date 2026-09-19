@extends('layouts.app')

@section('title', setting('brand_name', 'NITIP DI END'))

@section('content')
    {{-- Hero --}}
    <x-hero />

    {{-- Categories Section --}}
    <section class="py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-[#333333] mb-2 text-center">
                Jelajahi Kategori
            </h2>
            <p class="text-[#999999] text-center mb-8 max-w-xl mx-auto">
                Nemunya di mana? Titip aja.
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($categories as $category)
                    <x-category-card :category="$category" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured Products --}}
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-[#333333] mb-2">
                Lagi Banyak Dititipin 🔥
            </h2>
            <p class="text-[#999999] mb-8">Produk pilihan yang lagi viral dan banyak dicari.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="col-span-full text-center py-12 text-[#999999]">
                        Belum ada produk unggulan.
                    </p>
                @endforelse
            </div>

            @if($featuredProducts->count() > 0)
                <div class="text-center mt-8">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center px-6 py-3 text-rose-600 font-medium hover:text-rose-800 transition-colors">
                        Lihat Semua Produk
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Popular Products --}}
    @if($popularProducts->isNotEmpty())
    <section class="py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-[#333333] mb-2">
                Produk Favorit Pelanggan
            </h2>
            <p class="text-[#999999] mb-8">Barang incaran, tinggal nitip.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($popularProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <section class="py-20 bg-gradient-to-r from-rose-500 to-rose-600 text-white text-center">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Barang incaran, tinggal nitip.
            </h2>
            <p class="text-lg mb-8 opacity-90">
                Scroll boleh, checkout belakangan 😆
            </p>
            <a href="{{ route('cart.index') }}"
               class="inline-flex items-center px-8 py-4 bg-white text-rose-600 font-bold rounded-full shadow-lg hover:shadow-xl transition-all duration-200">
                Lihat Titipan Kamu
            </a>
        </div>
    </section>
@endsection