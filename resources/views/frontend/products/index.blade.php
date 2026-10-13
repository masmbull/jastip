@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <x-page-header title="Semua Produk" :subtitle="$products->total() . ' produk tersedia'"
                   class="mb-8 animate-fade-up">
        <div>
            <label for="sortSelect" class="sr-only">Urutkan produk</label>
            <select id="sortSelect"
                    class="px-4 py-2.5 border border-[#E2E8F0] dark:border-[#404854] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 text-sm bg-white dark:bg-[#23252b] text-[#1E293B] dark:text-[#f1f5f9]">
                <option value="popular" {{ ($sortBy ?? 'popular') === 'popular' ? 'selected' : '' }}>Produk Populer</option>
                <option value="terbaru" {{ ($sortBy ?? '') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="termurah" {{ ($sortBy ?? '') === 'termurah' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="termahal" {{ ($sortBy ?? '') === 'termahal' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </div>
    </x-page-header>

    {{-- Filters --}}
    <div id="filters" class="mb-6 animate-fade-up" style="animation-delay: 120ms">
        @if(request('category'))
            <div class="flex items-center gap-2 mb-2">
                <span class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Kategori:</span>
                <span class="text-sm font-medium text-orange-600 dark:text-orange-400">{{ request('category') }}</span>
                <a href="{{ route('products.index') }}" class="text-xs text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">| Hapus</a>
            </div>
        @endif

        @if($categories && $categories->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mt-3">
                <span class="text-xs text-[#64748B] dark:text-[#cbd5e1]">Urutkan / Filter:</span>
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', array_merge(request()->except(['page','sort']), ['category' => $cat->slug])) }}"
                       class="px-3 py-1.5 text-xs rounded-full border transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 {{ request('category') === $cat->slug ? 'border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10' : 'border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1] hover:border-orange-300' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Product Grid --}}
    @if($products->isNotEmpty())
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @else
        <x-empty-state icon="cube" title="Tidak ada produk ditemukan." class="animate-fade-up">
            Coba ubah filter pencarianmu.
        </x-empty-state>
    @endif

    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="mt-12">
            {{ $products->links('components.pagination') }}
        </div>
    @endif

    <script>
        document.getElementById('sortSelect')?.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.search = url.searchParams.toString();
        });
    </script>
</div>
@endsection