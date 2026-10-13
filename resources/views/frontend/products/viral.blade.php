@extends('layouts.app')

@section('title', 'Produk Viral')

@php
    $metaDescription = 'Daftar produk viral Indonesia 2024-2025: skincare lokal, hijab, sneakers, snack Jepang, Labubu, sampai parfum lokal. Harga pasaran & estimasi ongkir tersedia.';
    $ogImage = asset('images/viral/skintific-5x-ceramide-moisture-gel.jpg');
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Hero / Header --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-100 via-[#ecfeff] to-orange-50 dark:from-[#23252b] dark:via-[#1a1a1a] dark:to-[#23252b] border border-[#a5f3fc] dark:border-[#404854] p-6 sm:p-10 mb-10 animate-fade-up">
        <div class="relative z-10 max-w-3xl">
            <x-badge tone="brand" class="px-3 py-1 text-sm uppercase tracking-wide font-bold">
                <x-icon name="sparkles" class="w-4 h-4" /> Trending 2024-2025
            </x-badge>
            <h1 class="mt-4 text-2xl sm:text-3xl md:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Produk Viral Indonesia</h1>
            <p class="mt-3 text-[#64748B] dark:text-[#cbd5e1] leading-relaxed">
                Kurasi produk yang paling ramai diburu di TikTok Shop, Shopee, dan Google Trends Indonesia:
                mulai skincare lokal, hijab, sneakers, snack Jepang, sampai Labubu. Semua bisa kamu titip lewat
                {{ setting('brand_name', 'NITIP DI END') }}, lengkap dengan harga pasaran dan estimasi ongkir.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <x-btn as="a" :href="route('shipping.index')" class="px-5 py-2.5 rounded-full">Cek Estimasi Ongkir</x-btn>
                <a href="{{ whatsapp_url(setting('whatsapp')) }}" target="_blank" rel="noopener noreferrer"
                   class="px-5 py-2.5 bg-white dark:bg-[#23252b] border border-orange-300 dark:border-orange-500/40 text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Chat Admin
                </a>
            </div>

            <dl class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-[#23252b] rounded-xl border border-[#E2E8F0] dark:border-[#404854] p-4">
                    <dt class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Produk viral</dt>
                    <dd class="text-2xl font-bold text-orange-600 dark:text-orange-400 num">{{ $totalViral }}</dd>
                </div>
                <div class="bg-white dark:bg-[#23252b] rounded-xl border border-[#E2E8F0] dark:border-[#404854] p-4">
                    <dt class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Total terjual</dt>
                    <dd class="text-2xl font-bold text-orange-600 dark:text-orange-400 num">{{ number_format($totalSold / 1000, 0, ',', '.') }} rb+</dd>
                </div>
                <div class="bg-white dark:bg-[#23252b] rounded-xl border border-[#E2E8F0] dark:border-[#404854] p-4 col-span-2 sm:col-span-1">
                    <dt class="text-xs uppercase tracking-wide text-[#64748B] dark:text-[#cbd5e1]">Kategori</dt>
                    <dd class="text-2xl font-bold text-orange-600 dark:text-orange-400 num">{{ $categories->count() }}</dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- Filter kategori --}}
    <div class="flex flex-wrap items-center gap-2 mb-8 animate-fade-up">
        <span class="text-xs text-[#64748B] dark:text-[#cbd5e1] mr-1">Filter kategori:</span>
        <a href="{{ route('products.viral') }}"
           class="px-3 py-1.5 text-xs rounded-full border transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 {{ !request('category') ? 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10' : 'border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1] hover:border-orange-300' }}">
            Semua (<span class="num">{{ $totalViral }}</span>)
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('products.viral', ['category' => $cat->slug]) }}"
               class="px-3 py-1.5 text-xs rounded-full border transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 {{ request('category') === $cat->slug ? 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10' : 'border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1] hover:border-orange-300' }}">
                {{ $cat->name }} (<span class="num">{{ $cat->viral_count }}</span>)
            </a>
        @endforeach
    </div>

    {{-- Grid produk viral --}}
    @if($products->isNotEmpty())
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $product)
                @php
                    $isTopRank = $product->viral_rank && $product->viral_rank <= 3;
                @endphp
                <article class="bg-white dark:bg-[#23252b] rounded-xl border border-[#E2E8F0] dark:border-[#404854] shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group flex flex-col animate-fade-up">
                    <div class="relative aspect-square overflow-hidden bg-[#F1F5F9] dark:bg-[#2e323b]">
                        <img src="{{ $product->image_url }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             loading="lazy" decoding="async" sizes="(max-width: 640px) 50vw, 25vw">

                        @if($product->viral_rank)
                            <x-badge :tone="$isTopRank ? 'brand' : 'neutral'" class="absolute top-3 left-3 font-bold shadow-sm">
                                #<span class="num">{{ $product->viral_rank }}</span> Viral
                            </x-badge>
                        @endif

                        @if($product->stock <= 0)
                            <x-badge tone="neutral" class="absolute top-3 right-3 font-bold shadow-sm">Habis</x-badge>
                        @endif
                    </div>

                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase font-medium">{{ $product->category->name ?? 'Lainnya' }}</p>
                            @if($product->sold_text)
                                <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ $product->sold_text }}</span>
                            @endif
                        </div>

                        <h2 class="font-bold text-base text-[#1E293B] dark:text-[#f1f5f9] line-clamp-2">{{ $product->name }}</h2>

                        @if($product->brand)
                            <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">oleh {{ $product->brand }}</p>
                        @endif

                        <div class="flex items-center gap-1 mt-2">
                            @if($product->rating_text)
                                <x-icon name="star" class="w-4 h-4 text-amber-500" />
                                <span class="text-xs font-semibold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $product->rating_text }}</span>
                                <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">rating</span>
                            @else
                                <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Belum ada rating</span>
                            @endif
                        </div>

                        <p class="text-orange-600 dark:text-orange-400 font-bold text-lg mt-3 num">{{ $product->formatted_price_range }}</p>
                        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] -mt-1 mb-3">harga pasaran di Indonesia</p>

                        @if($product->stock <= 0)
                            <x-badge tone="neutral" class="self-start mb-4">Stok Habis</x-badge>
                        @else
                            <x-badge tone="success" class="self-start mb-4">Tersedia (<span class="num">{{ $product->stock }}</span>)</x-badge>
                        @endif

                        <div class="mt-auto flex gap-2">
                            <x-btn as="a" :href="route('products.show', $product->slug)" variant="brand" class="flex-1 rounded-full">
                                Lihat Detail
                            </x-btn>
                            @if($product->stock > 0)
                                <form method="POST" action="{{ route('cart.add', ['product' => $product->id]) }}" data-async>
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                            class="w-11 h-11 rounded-full border border-orange-300 dark:border-orange-500/40 text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-orange-300"
                                            aria-label="Tambah {{ $product->name }} ke titipan" title="Tambah ke titipan">
                                        <x-icon name="plus" class="w-5 h-5" />
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if($products->hasPages())
            <div class="mt-12">
                {{ $products->links('components.pagination') }}
            </div>
        @endif
    @else
        <x-empty-state icon="sparkles" title="Belum ada produk viral di kategori ini.">
            <a href="{{ route('products.viral') }}" class="inline-block mt-2 text-sm text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium">
                Lihat semua produk viral
            </a>
        </x-empty-state>
    @endif

    {{-- Sumber data --}}
    <section class="mt-16 rounded-2xl bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#404854] p-6 sm:p-8">
        <h2 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9]">Dari mana daftar ini diambil?</h2>
        <p class="mt-2 text-sm text-[#64748B] dark:text-[#cbd5e1] leading-relaxed">
            Daftar disusun dari pantauan produk terlaris TikTok Shop &amp; Shopee Indonesia serta Google Trends
            Indonesia periode 2024-2025. Angka harga adalah <strong>rentang harga pasaran</strong> (bukan harga
            toko kami), harga final mengikuti harga asli di marketplace saat kamu nitip.
        </p>
        <p class="mt-3 text-sm text-[#64748B] dark:text-[#cbd5e1]">
            Mau titip sekarang?
            <a href="{{ whatsapp_url(setting('whatsapp')) }}" target="_blank" rel="noopener noreferrer" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold">Chat Admin</a>
            atau <a href="{{ route('shipping.index') }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold">cek estimasi ongkir</a> dulu.
        </p>
    </section>
</div>
@endsection