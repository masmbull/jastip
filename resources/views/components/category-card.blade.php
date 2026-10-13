@props(['category' => null])

@php
    $category = $category ?? $attributes->get('category');
    $categoryImage = $category->products->first()?->image_url;
@endphp

<a href="{{ route('categories.show', $category->slug) }}"
   class="block bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-4 text-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 group animate-fade-up focus:outline-none focus:ring-2 focus:ring-orange-300">

    @if($categoryImage)
        <img src="{{ $categoryImage }}"
             alt="{{ $category->name }}"
             class="w-16 h-16 sm:w-20 sm:h-20 mx-auto rounded-full object-cover mb-3 ring-4 ring-orange-100 dark:ring-orange-500/20 group-hover:ring-orange-200 transition-all"
             loading="lazy" decoding="async">
    @else
        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto rounded-full bg-orange-100 dark:bg-orange-500/15 flex items-center justify-center mb-3">
            {{-- ponytail: product image wins; emoji icon is the admin-managed fallback --}}
            @if($category->icon)
                <span class="text-3xl" aria-hidden="true">{{ $category->icon }}</span>
            @else
                <x-icon name="cube" class="w-8 h-8 text-orange-500 dark:text-orange-400" />
            @endif
        </div>
    @endif

    <h3 class="font-bold text-base sm:text-lg text-[#1E293B] dark:text-[#f1f5f9] mb-1 line-clamp-2">{{ $category->name }}</h3>
    <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]"><span class="num">{{ $category->products_count }}</span> produk</p>
</a>