@props(['product' => null])

@php
    $product = $product ?? $attributes->get('product');
@endphp

<article class="nb-card nb-card-lift p-0! overflow-hidden group flex h-full flex-col">
    <div class="relative aspect-[4/3] overflow-hidden bg-[#FAF6EF] dark:bg-[#262626] border-b-2 border-[#141414] dark:border-[#FAF6EF]">
        @if($product)
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="nb-product-img w-full h-full object-cover"
                 loading="lazy" decoding="async" sizes="(max-width: 640px) 50vw, (max-width: 1200px) 33vw, 25vw">
            @if($product->is_featured)
                <x-badge tone="brand" class="absolute top-2.5 left-2.5 font-black z-[1]">Unggulan</x-badge>
            @endif
            @if(! $product->isInStock())
                <x-badge tone="neutral" class="absolute top-2.5 right-2.5 font-black z-[1]">Habis</x-badge>
            @endif

            @if($product->isInStock())
                <form method="POST"
                      action="{{ route('cart.add', ['product' => $product->id]) }}"
                      data-async
                      class="absolute bottom-2.5 right-2.5 sm:translate-y-2 sm:opacity-0 sm:group-hover:translate-y-0 sm:group-hover:opacity-100 sm:group-focus-within:translate-y-0 sm:group-focus-within:opacity-100 transition-all duration-150 max-sm:translate-y-0 max-sm:opacity-100">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="nb-btn w-11 h-11 p-0! bg-[#FFC800] text-[#141414]"
                            aria-label="Tambah {{ $product->name }} ke titipan" title="Tambah ke titipan">
                        <x-icon name="bag" class="w-5 h-5" stroke="2" />
                    </button>
                </form>
            @endif

            @auth
                <button type="button"
                        data-wishlist-toggle
                        data-url="{{ route('wishlist.toggle', ['product' => $product->id]) }}"
                        class="absolute {{ $product->isInStock() ? 'top-2.5 right-2.5' : 'top-12 right-2.5' }} w-11 h-11 bg-white dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[8px] text-[#991B1B] dark:text-[#FECACA] flex items-center justify-center z-[1] hover:bg-[#FEE2E2] transition-colors"
                        title="Tambah ke wishlist" aria-label="Tambah {{ $product->name }} ke wishlist">
                    <x-icon name="heart" class="w-5 h-5" stroke="2" />
                </button>
            @endauth
        @else
            <div class="w-full h-full bg-[#FAF6EF] dark:bg-[#262626] flex items-center justify-center">
                <x-icon name="cube" class="w-10 h-10 text-[#334155]" />
            </div>
        @endif
    </div>

    <div class="p-4 flex flex-col flex-1">
        @if($product)
            <div class="flex items-center justify-between gap-2 mb-1">
                <p class="text-[11px] text-[#334155] dark:text-[#A8A29E] font-black uppercase tracking-wider truncate">{{ $product->category->name ?? 'Lainnya' }}</p>
                @if($product->sold_text)
                    <span class="text-[11px] font-bold text-[#334155] dark:text-[#A8A29E] num shrink-0">{{ $product->sold_text }}</span>
                @endif
            </div>
            <h3 class="font-display font-extrabold text-[15px] leading-snug mb-1 line-clamp-2 min-h-[2.6em] text-[#141414] dark:text-[#FAF6EF]">{{ $product->name }}</h3>

            @if($product->brand)
                <p class="text-xs font-medium text-[#334155] dark:text-[#A8A29E] mb-1">oleh {{ $product->brand }}</p>
            @endif

            @if($product->rating_text)
                <p class="text-xs font-medium text-[#334155] dark:text-[#A8A29E] mb-2 flex items-center gap-1">
                    <x-icon name="star" class="w-4 h-4 text-[#B45309]" />
                    <span class="font-black num">{{ $product->rating_text }}</span>
                    <span>rating</span>
                </p>
            @endif

            <div class="mt-auto pt-3">
            <p class="text-[#141414] dark:text-[#FAF6EF] font-black text-lg mb-1 num">
                {{ $product->price_max ? $product->formatted_price_range : $product->formatted_price }}
            </p>
            <p class="mb-2 min-h-4 text-xs font-medium text-[#334155] dark:text-[#A8A29E]">{{ $product->price_max ? 'harga pasaran' : '' }}</p>

            <div class="mb-3">
                @php($badge = $product->availability_badge)
                <x-badge :tone="$badge['class'] === 'bg-red-100 text-red-700' ? 'danger' : ($badge['class'] === 'bg-amber-100 text-amber-700' ? 'warning' : 'success')">
                    <span class="num">{{ $badge['text'] }}</span>
                </x-badge>
            </div>

                @if($product->isInStock())
                    <x-btn as="a" :href="route('products.show', $product->slug)" variant="brand" class="w-full">
                        Lihat Detail
                    </x-btn>
                @else
                    <button disabled
                            class="w-full inline-flex items-center justify-center min-h-[44px] px-4 py-2 bg-[#FAF6EF] dark:bg-[#262626] border-2 border-[#141414]/20 dark:border-[#FAF6EF]/20 text-[#334155] dark:text-[#A8A29E] font-bold text-sm cursor-not-allowed rounded-[8px]">
                        Stok Habis
                    </button>
                @endif
            </div>
        @else
            <p class="text-sm font-medium text-[#334155]">Data produk tidak tersedia.</p>
        @endif
    </div>
</article>
