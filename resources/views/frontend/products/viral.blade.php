@extends('layouts.app')

@section('title', 'Produk Viral')

@php
    $metaDescription = 'Daftar produk viral Indonesia 2024–2025: skincare lokal, hijab, sneakers, snack Jepang, Labubu, sampai parfum lokal. Harga pasaran & estimasi ongkir tersedia.';
    $ogImage = asset('images/viral/skintific-5x-ceramide-moisture-gel.jpg');
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Hero / Header --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-100 via-[#FDF6EC] to-orange-50 border border-[#FED7AA] p-6 sm:p-10 mb-10 animate-fade-up">
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500 text-white text-xs font-bold uppercase tracking-wide">
                🔥 Trending 2024–2025
            </span>
            <h1 class="mt-4 text-3xl md:text-4xl font-bold text-[#1E293B]">Produk Viral Indonesia</h1>
            <p class="mt-3 text-[#475569] leading-relaxed">
                Kurasi produk yang paling ramai diburu di TikTok Shop, Shopee, dan Google Trends Indonesia —
                mulai skincare lokal, hijab, sneakers, snack Jepang, sampai Labubu. Semua bisa kamu titip lewat
                {{ setting('brand_name', 'NITIP DI END') }}, lengkap dengan harga pasaran dan estimasi ongkir.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('shipping.index') }}"
                   class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Cek Estimasi Ongkir
                </a>
                <a href="{{ whatsapp_url(setting('whatsapp')) }}" target="_blank" rel="noopener noreferrer"
                   class="px-5 py-2.5 bg-white border border-orange-300 text-orange-600 hover:bg-orange-50 font-semibold rounded-full text-sm transition-colors">
                    Chat Admin
                </a>
            </div>

            <dl class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-[#E2E8F0] p-4">
                    <dt class="text-xs uppercase tracking-wide text-[#94A3B8]">Produk viral</dt>
                    <dd class="text-2xl font-bold text-orange-600">{{ $totalViral }}</dd>
                </div>
                <div class="bg-white rounded-xl border border-[#E2E8F0] p-4">
                    <dt class="text-xs uppercase tracking-wide text-[#94A3B8]">Total terjual</dt>
                    <dd class="text-2xl font-bold text-orange-600">{{ number_format($totalSold / 1000, 0, ',', '.') }} rb+</dd>
                </div>
                <div class="bg-white rounded-xl border border-[#E2E8F0] p-4 col-span-2 sm:col-span-1">
                    <dt class="text-xs uppercase tracking-wide text-[#94A3B8]">Kategori</dt>
                    <dd class="text-2xl font-bold text-orange-600">{{ $categories->count() }}</dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- Filter kategori --}}
    <div class="flex flex-wrap items-center gap-2 mb-8 animate-fade-up">
        <span class="text-xs text-[#94A3B8] mr-1">Filter kategori:</span>
        <a href="{{ route('products.viral') }}"
           class="px-3 py-1 text-xs rounded-full border transition-colors {{ !request('category') ? 'border-orange-500 text-orange-600 bg-orange-50' : 'border-[#E2E8F0] text-[#64748B] hover:border-orange-300' }}">
            Semua ({{ $totalViral }})
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('products.viral', ['category' => $cat->slug]) }}"
               class="px-3 py-1 text-xs rounded-full border transition-colors {{ request('category') === $cat->slug ? 'border-orange-500 text-orange-600 bg-orange-50' : 'border-[#E2E8F0] text-[#64748B] hover:border-orange-300' }}">
                {{ $cat->name }} ({{ $cat->viral_count }})
            </a>
        @endforeach
    </div>

    {{-- Grid produk viral --}}
    @if($products->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                @php
                    $isTopRank = $product->viral_rank && $product->viral_rank <= 3;
                @endphp
                <article class="bg-white rounded-xl border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col">
                    <div class="relative aspect-square overflow-hidden bg-[#F1F5F9]">
                        <img src="{{ $product->image_url }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             loading="lazy" decoding="async">

                        @if($product->viral_rank)
                            <span class="absolute top-3 left-3 {{ $isTopRank ? 'bg-orange-500 text-white' : 'bg-slate-600 text-white' }} text-xs font-bold px-2.5 py-1 rounded-full">
                                #{{ $product->viral_rank }} Viral
                            </span>
                        @endif

                        @if($product->stock <= 0)
                            <span class="absolute top-3 right-3 bg-slate-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                                Habis
                            </span>
                        @endif
                    </div>

                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-xs text-[#94A3B8] uppercase font-medium">{{ $product->category->name ?? 'Lainnya' }}</p>
                            @if($product->sold_text)
                                <span class="text-xs text-[#64748B]">{{ $product->sold_text }}</span>
                            @endif
                        </div>

                        <h2 class="font-bold text-base text-[#1E293B] line-clamp-2">{{ $product->name }}</h2>

                        @if($product->brand)
                            <p class="text-xs text-[#64748B] mt-1">oleh {{ $product->brand }}</p>
                        @endif

                        <div class="flex items-center gap-1 mt-2">
                            @if($product->rating_text)
                                <span class="text-amber-500 text-sm">★</span>
                                <span class="text-xs font-semibold text-[#1E293B]">{{ $product->rating_text }}</span>
                                <span class="text-xs text-[#94A3B8]">rating</span>
                            @else
                                <span class="text-xs text-[#94A3B8]">Belum ada rating</span>
                            @endif
                        </div>

                        <p class="text-orange-600 font-bold text-lg mt-3">{{ $product->formatted_price_range }}</p>
                        <p class="text-xs text-[#94A3B8] -mt-1 mb-3">harga pasaran di Indonesia</p>

                        @if($product->stock <= 0)
                            <span class="self-start px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-600 mb-4">Stok Habis</span>
                        @else
                            <span class="self-start px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 mb-4">Tersedia ({{ $product->stock }})</span>
                        @endif

                        <div class="mt-auto flex gap-2">
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="flex-1 text-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                                Lihat Detail
                            </a>
                            @if($product->stock > 0)
                                <form method="POST" action="{{ route('cart.add', ['product' => $product->id]) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                            class="w-10 h-10 rounded-full border border-orange-300 text-orange-600 hover:bg-orange-50 flex items-center justify-center"
                                            title="Tambah ke keranjang">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
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
                {{ $products->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-16">
            <div class="w-20 h-20 mx-auto bg-[#F1F5F9] rounded-full mb-4 flex items-center justify-center text-3xl">🔥</div>
            <p class="text-[#94A3B8] font-medium">Belum ada produk viral di kategori ini.</p>
            <a href="{{ route('products.viral') }}" class="inline-block mt-4 text-sm text-orange-600 hover:text-orange-700 font-medium">
                Lihat semua produk viral
            </a>
        </div>
    @endif

    {{-- Sumber data --}}
    <section class="mt-16 rounded-2xl bg-white border border-[#E2E8F0] p-6 sm:p-8">
        <h2 class="text-lg font-bold text-[#1E293B]">Dari mana daftar ini diambil?</h2>
        <p class="mt-2 text-sm text-[#475569] leading-relaxed">
            Daftar disusun dari pantauan produk terlaris TikTok Shop &amp; Shopee Indonesia serta Google Trends
            Indonesia periode 2024–2025. Angka harga adalah <strong>rentang harga pasaran</strong> (bukan harga
            toko kami) — harga final mengikuti harga asli di marketplace saat kamu nitip.
        </p>
        <p class="mt-3 text-sm text-[#475569]">
            Mau titip sekarang?
            <a href="{{ whatsapp_url(setting('whatsapp')) }}" target="_blank" rel="noopener noreferrer" class="text-orange-600 hover:text-orange-700 font-semibold">Chat Admin</a>
            atau <a href="{{ route('shipping.index') }}" class="text-orange-600 hover:text-orange-700 font-semibold">cek estimasi ongkir</a> dulu.
        </p>
    </section>
</div>
@endsection
