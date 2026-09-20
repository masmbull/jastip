@props(['category' => null])

@php
    $category = $category ?? $attributes->get('category');
@endphp

@php
    $icons = [
        'Parfum' => '🧴',
        'Tumbler' => '🥤',
        'Fashion' => '👗',
        'Beauty' => '💄',
        'Lifestyle' => '🏠',
        'Lainnya' => '📦',
    ];
@endphp

<a href="{{ route('categories.show', $category->slug) }}"
   class="block bg-white rounded-xl shadow-md p-6 text-center hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group animate-fade-up">

    <div class="w-16 h-16 mx-auto rounded-full bg-rose-100 flex items-center justify-center mb-3 group-hover:bg-rose-200 transition-colors">
        <span class="text-3xl">{{ $icons[$category->name] ?? '📦' }}</span>
    </div>

    <h3 class="font-bold text-lg text-[#333333] mb-1">{{ $category->name }}</h3>
    <p class="text-sm text-[#999999]">{{ $category->products_count }} produk</p>
</a>