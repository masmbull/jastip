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
                <span class="absolute top-3 left-3 bg-rose-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                    Unggulan
                </span>
            @endif
            @if($product->stock <= 0)
                <span class="absolute top-3 right-3 bg-gray-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                    Habis
                </span>
            @endif
        @else
            <div class="w-full h-full bg-gradient-to-br from-rose-100 to-rose-50 flex items-center justify-center animate-shimmer">
                <span class="text-4xl">📦</span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-4">
        @if($product)
            <p class="text-xs text-[#999999] uppercase font-medium mb-1">{{ $product->category->name ?? 'Lainnya' }}</p>
            <h3 class="font-bold text-lg text-[#333333] mb-2 line-clamp-1">{{ $product->name }}</h3>
            <p class="text-rose-600 font-bold text-lg mb-3">{{ $product->formatted_price }}</p>

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
                   class="block w-full text-center px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-full text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-rose-300">
                    Lihat Detail
                </a>
            @else
                <button disabled
                   class="block w-full text-center px-4 py-2 bg-gray-300 text-gray-500 font-medium rounded-full text-sm cursor-not-allowed">
                    Stok Habis
                </button>
            @endif
        @else
            <p class="text-sm text-[#999999]">Data produk tidak tersedia.</p>
        @endif
    </div>
</article>