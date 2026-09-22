@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        {{-- Product Image --}}
        <div class="relative animate-fade-up">
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="w-full rounded-xl shadow-lg object-cover aspect-square"
                 loading="lazy" decoding="async"
                 sizes="(max-width: 1024px) 100vw, 50vw">
            @if($product->is_featured)
                <span class="absolute top-4 left-4 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                    Produk Unggulan
                </span>
            @endif
            @if(!$product->isInStock())
                <span class="absolute top-4 right-4 bg-gray-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                    Stok Habis
                </span>
            @endif
        </div>

        {{-- Product Details --}}
        <div class="space-y-6 animate-fade-up" style="animation-delay: 120ms">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-sm text-orange-600 font-medium">{{ $product->category->name ?? 'Lainnya' }}</p>
                    @if($product->brand)
                        <span class="text-xs text-[#94A3B8]">·</span>
                        <p class="text-sm text-[#64748B]">oleh <span class="font-semibold text-[#1E293B]">{{ $product->brand }}</span></p>
                    @endif
                </div>
                <h1 class="text-3xl font-bold text-[#1E293B]">{{ $product->name }}</h1>

                @if($product->rating_text || $product->sold_text)
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm">
                        @if($product->rating_text)
                            <span class="flex items-center gap-1">
                                <span class="text-amber-500">★</span>
                                <span class="font-semibold text-[#1E293B]">{{ $product->rating_text }}</span>
                                <span class="text-[#94A3B8]">rating</span>
                            </span>
                        @endif
                        @if($product->sold_text)
                            <span class="text-[#94A3B8]">{{ $product->sold_text }}</span>
                        @endif
                    </div>
                @endif

                <div class="mt-3">
                    <p class="text-2xl font-bold text-orange-600">
                        {{ $product->price_max ? $product->formatted_price_range : $product->formatted_price }}
                    </p>
                    @if($product->price_max)
                        <p class="text-xs text-[#94A3B8] mt-0.5">rentang harga pasaran di Indonesia — hubungi admin untuk harga titipan terkini</p>
                    @endif
                </div>

                <p class="mt-3 text-sm text-[#64748B]">
                    Stok:
                    @if($product->isInStock())
                        <span class="font-semibold text-emerald-600">Tersedia ({{ $product->stock }})</span>
                    @else
                        <span class="font-semibold text-red-600">Habis</span>
                    @endif
                </p>

                @php
                    $shippingCost = (int) setting('shipping_cost', 0);
                    $minOrder = (int) setting('minimum_order', 0);
                @endphp
                <div class="mt-3 bg-[#F1F5F9] rounded-lg p-4 text-sm text-[#64748B] space-y-1.5">
                    <p>🚚 Ongkir: <span class="font-semibold text-[#1E293B]">{{ $shippingCost > 0 ? 'Rp ' . number_format($shippingCost, 0, ',', '.') : 'Gratis' }}</span> — sesuai kota tujuan, dihitung saat checkout.</p>
                    <p>🧮 Estimasi cepat: <a href="{{ route('shipping.index', ['weight' => 1]) }}" class="text-orange-600 hover:text-orange-700 font-semibold">cek ongkir ke kotamu</a> (15 ekspedisi).</p>
                    <p>🎁 Gratis ongkir untuk titipan di atas Rp150.000.</p>
                    @if($minOrder > 0)
                        <p>🛒 Minimal titip: Rp {{ number_format($minOrder, 0, ',', '.') }}</p>
                    @endif
                    <p>✅ COD & QRIS tersedia — garansi autentik 100%.</p>
                </div>

                @php
                    $badge = $product->availability_badge;
                @endphp
                <span class="inline-flex items-center px-3 py-1 text-sm rounded-full {{ $badge['class'] }}">
                    {{ $badge['text'] }}
                </span>
            </div>

            {{-- Price tiers (optional) --}}
            @if(isset($product->price_tiers) && $product->price_tiers)
                <div class="bg-[#F1F5F9] rounded-lg p-4">
                    <p class="text-sm font-medium text-[#1E293B] mb-2">Harga Grosir (opsional):</p>
                    @foreach($product->price_tiers as $tier)
                        <div class="flex justify-between text-sm">
                            <span>{{ $tier['min'] }}+ {{ $tier['unit'] ?? 'pcs' }}</span>
                            <span class="font-medium">{{ format_price($tier['price']) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div>
                <h3 class="text-lg font-semibold text-[#1E293B] mb-2">Deskripsi</h3>
                <div class="text-[#64748B] leading-relaxed space-y-3">
                                        {!! strip_tags($product->description_html ?? $product->description, '<p><br><strong><b><em><i><ul><ol><li><h2><h3><h4><h5><h6><blockquote><code><span><hr>') !!}
                </div>
            </div>

            @if($product->isInStock())
            <div class="pt-4 border-t border-[#E2E8F0]">
                <form id="addToCartForm"
                      method="POST"
                      action="{{ route('cart.add', ['product' => $product->id]) }}"
                      data-async>
                    @csrf
                    <div class="flex items-center space-x-4 mb-4">
                        <label class="text-sm font-medium text-[#1E293B]">Jumlah:</label>
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="decrementQty()"
                                    class="w-10 h-10 flex items-center justify-center bg-[#F1F5F9] rounded-full text-[#1E293B] hover:bg-orange-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                                </svg>
                            </button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                                   class="w-12 h-10 text-center text-lg font-medium border border-[#E2E8F0] rounded focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <button type="button" onclick="incrementQty({{ $product->stock }})"
                                    class="w-10 h-10 flex items-center justify-center bg-[#F1F5F9] rounded-full text-[#1E293B] hover:bg-orange-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m6-6H6"></path>
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs text-[#94A3B8]">Stok: {{ $product->isInStock() ? 'Tersedia (' . $product->stock . ')' : 'Habis' }}</span>
                    </div>

                    <button type="submit"
                            id="addToCartBtn"
                            class="w-full px-6 py-4 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300">
                        Tambah ke Titipan
                    </button>
                </form>

                                <a href="{{ \App\Services\WhatsappService::contactUrl('Saya ingin nitip ' . $product->name) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="mt-3 flex items-center justify-center w-full px-6 py-3 border-2 border-green-500 text-green-600 font-medium rounded-full hover:bg-green-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z" />
                    </svg>
                    Nitip via WhatsApp
                </a>
            </div>
            @else
            <div class="pt-4">
                <button disabled
                        class="w-full px-6 py-4 bg-gray-300 text-gray-500 font-medium rounded-full cursor-not-allowed">
                    Stok Habis
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Related Products --}}
    @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
    <section class="mt-16 animate-fade-up">
        <h2 class="text-2xl font-bold text-[#1E293B] mb-6">Produk Serupa</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
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