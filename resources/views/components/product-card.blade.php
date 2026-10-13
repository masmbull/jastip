@props(['product' => null])

@php
    $product = $product ?? $attributes->get('product');
@endphp

<article class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden hover:shadow-md transition-all duration-300 group animate-fade-up flex flex-col">
    {{-- Image --}}
    <div class="relative aspect-[4/3] overflow-hidden bg-[#F1F5F9] dark:bg-[#2e323b]">
        @if($product)
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                 loading="lazy" decoding="async" sizes="(max-width: 640px) 50vw, (max-width: 1200px) 33vw, 25vw">
            @if($product->is_featured)
                <x-badge tone="brand" class="absolute top-3 left-3 font-bold shadow-sm">Unggulan</x-badge>
            @endif
            @if(! $product->isInStock())
                <x-badge tone="neutral" class="absolute top-3 right-3 font-bold shadow-sm">Habis</x-badge>
            @endif

            @if($product->isInStock())
                {{-- Quick-add "tombol keranjang": appears on hover, async POST to cart --}}
                <form method="POST"
                      action="{{ route('cart.add', ['product' => $product->id]) }}"
                      data-async
                      class="absolute bottom-3 right-3 translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:translate-y-0 group-focus-within:opacity-100 transition-all duration-200">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-300 flex items-center justify-center"
                            aria-label="Tambah {{ $product->name }} ke titipan" title="Tambah ke titipan">
                        <x-icon name="cart" class="w-5 h-5" />
                    </button>
                </form>
            @endif

            @auth
                {{-- Wishlist heart: async toggle, top-right of image --}}
                <button type="button"
                        data-wishlist-toggle
                        data-url="{{ route('wishlist.toggle', ['product' => $product->id]) }}"
                        class="absolute top-3 right-3 w-11 h-11 rounded-full bg-white/90 hover:bg-white text-red-500 shadow-md flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-red-300"
                        title="Tambah ke wishlist" aria-label="Tambah {{ $product->name }} ke wishlist">
                    <x-icon name="heart" class="w-5 h-5" />
                </button>
            @endauth
        @else
            <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-[#2e323b] dark:to-[#23252b] flex items-center justify-center animate-shimmer">
                <x-icon name="cube" class="w-10 h-10 text-orange-400" />
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-4 flex flex-col flex-1">
        @if($product)
            <div class="flex items-center justify-between gap-2 mb-1">
                <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase font-medium">{{ $product->category->name ?? 'Lainnya' }}</p>
                @if($product->sold_text)
                    <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">{{ $product->sold_text }}</span>
                @endif
            </div>
            <h3 class="font-bold text-base text-[#1E293B] dark:text-[#f1f5f9] mb-1 line-clamp-2">{{ $product->name }}</h3>

            @if($product->brand)
                <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mb-1">oleh {{ $product->brand }}</p>
            @endif

            @if($product->rating_text)
                <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mb-2 flex items-center gap-1">
                    <x-icon name="star" class="w-4 h-4 text-amber-500" />
                    <span class="font-semibold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $product->rating_text }}</span>
                    <span>rating</span>
                </p>
            @endif

            <p class="text-orange-600 dark:text-orange-400 font-bold text-lg mb-1 num">
                {{ $product->price_max ? $product->formatted_price_range : $product->formatted_price }}
            </p>
            @if($product->price_max)
                <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mb-2">harga pasaran</p>
            @endif

            {{-- Availability --}}
            <div class="mb-3">
                @php($badge = $product->availability_badge)
                <x-badge :tone="$badge['class'] === 'bg-red-100 text-red-700' ? 'danger' : ($badge['class'] === 'bg-amber-100 text-amber-700' ? 'warning' : 'success')">
                    <span class="num">{{ $badge['text'] }}</span>
                </x-badge>
            </div>

            {{-- Action --}}
            <div class="mt-auto">
                @if($product->isInStock())
                    <x-btn as="a" :href="route('products.show', $product->slug)" variant="brand" class="w-full rounded-full">
                        Lihat Detail
                    </x-btn>
                @else
                    <button disabled
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#E2E8F0] dark:bg-[#2e323b] text-[#64748B] dark:text-[#cbd5e1] font-medium rounded-lg text-sm cursor-not-allowed">
                        Stok Habis
                    </button>
                @endif
            </div>
        @else
            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Data produk tidak tersedia.</p>
        @endif
    </div>
</article>