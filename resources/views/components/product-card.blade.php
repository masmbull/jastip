@props(['product' => null])

@php
    $product = $product ?? $attributes->get('product');
@endphp

<article class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group animate-fade-up">
    {{-- Image --}}
    <div class="relative aspect-[4/3] overflow-hidden">
        @if($product)
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                 loading="lazy" decoding="async" sizes="(max-width: 768px) 100vw, (max-width: 1200px) 33vw, 25vw">
                        @if($product->is_featured)
                <span class="absolute top-3 left-3 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                    Unggulan
                </span>
            @endif
            @if($product->stock <= 0)
                <span class="absolute top-3 right-3 bg-gray-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                    Habis
                </span>
            @endif

            @if($product->isInStock())
                {{-- Quick-add "tombol keranjang" — appears on hover, async POST to cart --}}
                <form method="POST"
                      action="{{ route('cart.add', ['product' => $product->id]) }}"
                      data-async
                      class="absolute bottom-3 right-3 translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-200">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-300 flex items-center justify-center"
                            title="Tambah ke keranjang">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M8 11h8m-2 4h-4M8 15l-2 4h8l-2-4"></path>
                        </svg>
                    </button>
                </form>
            @endif
        @else
            <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center animate-shimmer">
                <span class="text-4xl">📦</span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-4">
        @if($product)
            <div class="flex items-center justify-between gap-2 mb-1">
                <p class="text-xs text-[#94A3B8] uppercase font-medium">{{ $product->category->name ?? 'Lainnya' }}</p>
                @if($product->sold_text)
                    <span class="text-xs text-[#64748B]">{{ $product->sold_text }}</span>
                @endif
            </div>
            <h3 class="font-bold text-lg text-[#1E293B] mb-1 line-clamp-1">{{ $product->name }}</h3>

            @if($product->brand)
                <p class="text-xs text-[#64748B] mb-1">oleh {{ $product->brand }}</p>
            @endif

            @if($product->rating_text)
                <p class="text-xs text-[#64748B] mb-2 flex items-center gap-1">
                    <span class="text-amber-500">★</span>
                    <span class="font-semibold text-[#1E293B]">{{ $product->rating_text }}</span>
                    <span class="text-[#94A3B8]">rating</span>
                </p>
            @else
                <div class="mb-2"></div>
            @endif

            <p class="text-orange-600 font-bold text-lg mb-1">
                {{ $product->price_max ? $product->formatted_price_range : $product->formatted_price }}
            </p>
            @if($product->price_max)
                <p class="text-xs text-[#94A3B8] mb-2">harga pasaran</p>
            @endif

            {{-- Availability --}}
            <div class="mb-3">
                @php
                    $badge = $product->availability_badge;
                @endphp
                <span class="px-2 py-1 text-xs rounded-full {{ $badge['class'] }}">
                    {{ $badge['text'] }}
                </span>
            </div>

            {{-- Action --}}
            @if($product->isInStock())
                <a href="{{ route('products.show', $product->slug) }}"
                   class="block w-full text-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Lihat Detail
                </a>
            @else
                <button disabled
                   class="block w-full text-center px-4 py-2 bg-gray-300 text-gray-500 font-medium rounded-full text-sm cursor-not-allowed">
                    Stok Habis
                </button>
            @endif
        @else
            <p class="text-sm text-[#94A3B8]">Data produk tidak tersedia.</p>
        @endif
    </div>
</article>