@props(['category' => null])

@php
    $category = $category ?? $attributes->get('category');
    $categoryImage = $category->products->first()?->image_url;
@endphp

<a href="{{ route('categories.show', $category->slug) }}"
   class="block bg-white rounded-xl shadow-md p-4 text-center hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group animate-fade-up">

    @if($categoryImage)
        <img src="{{ $categoryImage }}"
             alt="{{ $category->name }}"
             class="w-20 h-20 mx-auto rounded-full object-cover mb-3 ring-4 ring-orange-100 group-hover:ring-orange-200 transition-all"
             loading="lazy" decoding="async">
    @else
        <div class="w-20 h-20 mx-auto rounded-full bg-orange-100 flex items-center justify-center mb-3">
            <span class="text-3xl">📦</span>
        </div>
    @endif

    <h3 class="font-bold text-lg text-[#1E293B] mb-1">{{ $category->name }}</h3>
    <p class="text-sm text-[#94A3B8]">{{ $category->products_count }} produk</p>
</a>