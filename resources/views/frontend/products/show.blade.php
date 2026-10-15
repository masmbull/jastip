@extends('layouts.app')

@section('title', $product->name)

@php
    // Meta khusus halaman produk: deskripsi & gambar ikut produk, supaya
    // pratinjau link (OG/Twitter) menampilkan foto + harga, bukan default situs.
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 155)
        ?: ($product->name . ' - ' . format_price($product->price) . ' di ' . setting('brand_name', 'NITIP DI END') . '.');
    $ogImage = $product->image_url ?: null;
@endphp

@push('seo')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->name,
    'description' => \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 300),
    'image' => $product->image_url ? [$product->image_url] : null,
    'sku' => $product->sku ?? null,
    'brand' => ['@type' => 'Brand', 'name' => setting('brand_name', 'NITIP DI END')],
    'offers' => [
        '@type' => 'Offer',
        'url' => url()->current(),
        'priceCurrency' => 'IDR',
        'price' => (float) $product->price,
        'availability' => ($product->stock ?? 0) > 0
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Produk', 'item' => route('products.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $product->name, 'item' => url()->current()],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        {{-- Product Image --}}
        <div class="relative animate-fade-up">
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="w-full rounded-xl shadow-lg object-cover aspect-square bg-[#F1F5F9] dark:bg-[#2e323b]"
                 loading="eager" decoding="async"
                 sizes="(max-width: 1024px) 100vw, 50vw">
            @if($product->is_featured)
                <x-badge tone="brand" class="absolute top-4 left-4 font-bold shadow-sm">Produk Unggulan</x-badge>
            @endif
            @if(!$product->isInStock())
                <x-badge tone="neutral" class="absolute top-4 right-4 font-bold shadow-sm">Stok Habis</x-badge>
            @endif
        </div>

        {{-- Product Details --}}
        <div class="space-y-6 animate-fade-up" style="animation-delay: 120ms">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">{{ $product->category->name ?? 'Lainnya' }}</p>
                    @if($product->brand)
                        <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]" aria-hidden="true">·</span>
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">oleh <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->brand }}</span></p>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->name }}</h1>

                @if($product->rating_text || $product->sold_text)
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm">
                        @if($product->rating_text)
                            <span class="flex items-center gap-1">
                                <x-icon name="star" class="w-4 h-4 text-amber-500" />
                                <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $product->rating_text }}</span>
                                <span class="text-[#64748B] dark:text-[#cbd5e1]">rating</span>
                            </span>
                        @endif
                        @if($product->sold_text)
                            <span class="text-[#64748B] dark:text-[#cbd5e1]">{{ $product->sold_text }}</span>
                        @endif
                    </div>
                @endif

                <a href="{{ route('products.reviews', $product) }}"
                   class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                    Lihat semua ulasan ({{ $product->getReviewCount() }})
                </a>

                <div class="mt-3">
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 num">
                        {{ $product->price_max ? $product->formatted_price_range : $product->formatted_price }}
                    </p>
                    @if($product->price_max)
                        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-0.5">rentang harga pasaran di Indonesia, hubungi admin untuk harga titipan terkini</p>
                    @endif
                    @if($product->effectiveFee() > 0)
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] mt-1">Biaya fee: <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $product->formatted_setbiaya_fee }}</span> / item</p>
                    @endif
                </div>

                <p class="mt-3 text-sm text-[#64748B] dark:text-[#cbd5e1]">
                    Stok:
                    @if($product->isInStock())
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">Tersedia (<span class="num">{{ $product->stock }}</span>)</span>
                    @else
                        <span class="font-semibold text-red-600 dark:text-red-400">Habis</span>
                    @endif
                </p>

                @php
                    $shippingCost = (int) setting('shipping_cost', 0);
                    $minOrder = (int) setting('minimum_order', 0);
                @endphp
                <div class="mt-3 bg-[#F1F5F9] dark:bg-[#2e323b] rounded-lg p-4 text-sm text-[#64748B] dark:text-[#cbd5e1] space-y-1.5">
                    <p class="flex items-center gap-2"><x-icon name="truck" class="w-4 h-4 text-orange-500 dark:text-orange-400 shrink-0" /> Ongkir: <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $shippingCost > 0 ? 'Rp ' . number_format($shippingCost, 0, ',', '.') : 'Gratis' }}</span> sesuai kota tujuan, dihitung saat checkout.</p>
                    <p class="flex items-center gap-2"><x-icon name="cube" class="w-4 h-4 text-orange-500 dark:text-orange-400 shrink-0" /> Estimasi cepat: <a href="{{ route('shipping.index', ['weight' => 1]) }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold">cek ongkir ke kotamu</a> (15 ekspedisi).</p>
                    <p class="flex items-center gap-2"><x-icon name="gift" class="w-4 h-4 text-orange-500 dark:text-orange-400 shrink-0" /> Gratis ongkir untuk titipan di atas Rp150.000.</p>
                    @if($minOrder > 0)
                        <p class="flex items-center gap-2"><x-icon name="cart" class="w-4 h-4 text-orange-500 dark:text-orange-400 shrink-0" /> Minimal titip: Rp <span class="num">{{ number_format($minOrder, 0, ',', '.') }}</span></p>
                    @endif
                    <p class="flex items-center gap-2"><x-icon name="check-circle" class="w-4 h-4 text-orange-500 dark:text-orange-400 shrink-0" /> COD &amp; QRIS tersedia, garansi autentik 100%.</p>
                </div>

                @php
                    $badge = $product->availability_badge;
                @endphp
                <x-badge :tone="$badge['class'] === 'bg-red-100 text-red-700' ? 'danger' : ($badge['class'] === 'bg-amber-100 text-amber-700' ? 'warning' : 'success')" class="mt-3 px-3 py-1 text-sm">
                    <span class="num">{{ $badge['text'] }}</span>
                </x-badge>
            </div>

            {{-- Price tiers (optional) --}}
            @if(isset($product->price_tiers) && $product->price_tiers)
                <div class="bg-[#F1F5F9] dark:bg-[#2e323b] rounded-lg p-4">
                    <p class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Harga Grosir (opsional):</p>
                    @foreach($product->price_tiers as $tier)
                        <div class="flex justify-between text-sm text-[#64748B] dark:text-[#cbd5e1]">
                            <span><span class="num">{{ $tier['min'] }}</span>+ {{ $tier['unit'] ?? 'pcs' }}</span>
                            <span class="font-medium num">{{ format_price($tier['price']) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div>
                <h3 class="text-lg font-semibold text-[#1E293B] dark:text-[#f1f5f9] mb-2">Deskripsi</h3>
                <div class="text-[#64748B] dark:text-[#cbd5e1] leading-relaxed space-y-3 prose prose-sm max-w-none dark:prose-invert">
                    {{ strip_tags($product->description_html ?? $product->description) }}
                </div>
            </div>

            @if($product->isInStock())
            <div class="pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
                <form id="addToCartForm"
                      method="POST"
                      action="{{ route('cart.add', ['product' => $product->id]) }}"
                      data-async>
                    @csrf
                    <div class="flex flex-wrap items-center gap-4 mb-4">
                        <span class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">Jumlah:</span>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="decrementQty()"
                                    class="w-11 h-11 flex items-center justify-center bg-[#F1F5F9] dark:bg-[#2e323b] rounded-full text-[#1E293B] dark:text-[#f1f5f9] hover:bg-orange-100 dark:hover:bg-orange-500/15 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300"
                                    aria-label="Kurangi jumlah" title="Kurangi jumlah">
                                <x-icon name="minus" class="w-5 h-5" />
                            </button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                                   aria-label="Jumlah"
                                   class="w-14 h-11 text-center text-lg font-medium num border border-[#E2E8F0] dark:border-[#404854] rounded bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <button type="button" onclick="incrementQty({{ $product->stock }})"
                                    class="w-11 h-11 flex items-center justify-center bg-[#F1F5F9] dark:bg-[#2e323b] rounded-full text-[#1E293B] dark:text-[#f1f5f9] hover:bg-orange-100 dark:hover:bg-orange-500/15 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300"
                                    aria-label="Tambah jumlah" title="Tambah jumlah">
                                <x-icon name="plus" class="w-5 h-5" />
                            </button>
                        </div>
                        <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Stok: {{ $product->isInStock() ? 'Tersedia (' . $product->stock . ')' : 'Habis' }}</span>
                    </div>

                    <button type="submit"
                            id="addToCartBtn"
                            class="w-full px-6 py-4 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                        Tambah ke Titipan
                    </button>
                </form>

                <a href="{{ \App\Services\WhatsappService::contactUrl('Saya ingin nitip ' . $product->name) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="mt-3 flex items-center justify-center gap-2 w-full px-6 py-3 border-2 border-green-500 text-green-600 dark:text-green-400 font-medium rounded-full hover:bg-green-50 dark:hover:bg-green-500/10 transition-colors focus:outline-none focus:ring-2 focus:ring-green-300">
                    <x-icon name="chat" class="w-5 h-5" />
                    Nitip via WhatsApp
                </a>

                @auth
                <button type="button"
                        data-wishlist-toggle
                        data-url="{{ route('wishlist.toggle', ['product' => $product->id]) }}"
                        class="mt-3 flex items-center justify-center gap-2 w-full px-6 py-3 border-2 border-red-400 text-red-500 font-medium rounded-full hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors focus:outline-none focus:ring-2 focus:ring-red-300">
                    <x-icon name="heart" class="w-5 h-5" />
                    <span data-wishlist-label>Tambah ke Wishlist</span>
                </button>
                @endauth
            </div>
            @else
            <div class="pt-4">
                <button disabled
                        class="w-full px-6 py-4 bg-[#E2E8F0] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] font-medium rounded-full cursor-not-allowed">
                    Stok Habis
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Related Products --}}
    @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
    <section class="mt-16 animate-fade-up">
        <h2 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-6">Produk Serupa</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 nb-rainbow">
            @foreach($relatedProducts as $related)
                <x-product-card :product="$related" />
            @endforeach
        </div>
    </section>
    @endif
</div>

<script>
    function decrementQty() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) > parseInt(input.min || 1)) {
            input.value = parseInt(input.value) - 1;
        }
    }
    function incrementQty(max) {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }
</script>
@endsection