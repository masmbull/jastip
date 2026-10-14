@props(['category' => null])

@php
    $category = $category ?? $attributes->get('category');
    $categoryImage = $category->products->first()?->image_url;
@endphp

<a href="{{ route('categories.show', $category->slug) }}"
   class="nb-card block p-4 text-center hover:-translate-y-0.5 transition-transform group">

    @if($categoryImage)
        <img src="{{ $categoryImage }}"
             alt="{{ $category->name }}"
             class="w-16 h-16 sm:w-20 sm:h-20 mx-auto rounded-[10px] border-2 border-[#141414] dark:border-[#FAF6EF] object-cover mb-3"
             loading="lazy" decoding="async">
    @else
        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto rounded-[10px] border-2 border-[#141414] dark:border-[#FAF6EF] bg-[#FFC800] flex items-center justify-center mb-3 text-[#141414]">
            @if($category->icon)
                <span class="text-3xl" aria-hidden="true">{{ $category->icon }}</span>
            @else
                <x-icon name="cube" class="w-8 h-8" />
            @endif
        </div>
    @endif

    <h3 class="font-black text-base sm:text-lg leading-snug line-clamp-2">{{ $category->name }}</h3>
    <p class="text-sm font-bold text-[#334155] dark:text-[#D6D3D1] mt-0.5"><span class="num">{{ $category->products_count }}</span> produk</p>
</a>
