@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Hero --}}
    <x-hero />

    {{-- Category Section --}}
    <section class="py-12 md:py-16 reveal">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-3">Jelajahi Kategori</h2>
            <p class="text-[#64748B] dark:text-[#cbd5e1]">Temukan produk berdasarkan kategori yang kamu inginkan.</p>
        </div>

        @if($categories->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <x-category-card :category="$category" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="tag" title="Belum ada kategori tersedia." />
        @endif
    </section>

    {{-- Featured Products --}}
    <section class="py-12 md:py-16 bg-white dark:bg-[#23252b] rounded-2xl shadow-sm reveal">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-3">Produk Unggulan</h2>
            <p class="text-[#64748B] dark:text-[#cbd5e1]">Pilihan produk yang sedang ramai dibeli.</p>
        </div>

        @if($featuredProducts->isNotEmpty())
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="cube" title="Belum ada produk." />
        @endif

        <div class="text-center mt-10">
            <x-btn as="a" :href="route('products.index')" class="px-8 py-3 rounded-full shadow-md">
                Lihat Semua Produk
            </x-btn>
        </div>
    </section>

    {{-- Popular Products --}}
    <section class="py-12 md:py-16 reveal">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-3">Produk Populer</h2>
            <p class="text-[#64748B] dark:text-[#cbd5e1]">Produk terlaris pilihan customers.</p>
        </div>

        @if($popularProducts->isNotEmpty())
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($popularProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="cube" title="Belum ada produk populer." />
        @endif
    </section>

    {{-- Produk Viral --}}
    @if(isset($viralProducts) && $viralProducts->isNotEmpty())
        <section class="py-12 md:py-16 reveal">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <x-badge tone="brand" class="uppercase tracking-wide font-bold">Trending 2024-2025</x-badge>
                    <h2 class="mt-3 text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Produk Viral</h2>
                    <p class="mt-2 text-[#64748B] dark:text-[#cbd5e1]">Produk yang sedang ramai diburu di TikTok Shop &amp; Shopee Indonesia.</p>
                </div>
                <x-btn as="a" :href="route('products.viral')" class="px-5 py-2.5 rounded-full">Lihat Semua</x-btn>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($viralProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Call to Action --}}
    <section class="py-16 md:py-20 bg-gradient-to-r from-orange-500 to-orange-600 rounded-3xl shadow-xl text-center text-white mb-12 reveal">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Butuh Bantuan?</h2>
        <p class="text-lg md:text-xl text-orange-100 mb-6 max-w-2xl mx-auto">
            Ada pertanyaan? Hubungi admin kami via WhatsApp untuk bantuan cepat.
        </p>
        <a href="{{ \App\Services\WhatsappService::contactUrl('Halo Kak, saya mau tanya soal nitipan di ' . setting('brand_name', 'NITIP DI END')) }}"
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-orange-600 font-bold rounded-full shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white">
            <x-icon name="chat" class="w-5 h-5" />
            Chat Admin
        </a>
    </section>

</div>
@endsection