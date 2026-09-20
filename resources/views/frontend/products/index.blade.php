@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 animate-fade-up">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-[#333333] mb-2">Semua Produk</h1>
            <p class="text-[#999999]">{{ $products->total() }} produk tersedia</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <select id="sortSelect"
                    class="px-4 py-2 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300 text-sm bg-white">
                <option value="popular" {{ ($sortBy ?? 'popular') === 'popular' ? 'selected' : '' }}>Produk Populer</option>
                <option value="terbaru" {{ ($sortBy ?? '') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="termurah" {{ ($sortBy ?? '') === 'termurah' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="termahal" {{ ($sortBy ?? '') === 'termahal' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </div>
    </div>

    {{-- Filters --}}
    <div id="filters" class="mb-6 animate-fade-up" style="animation-delay: 120ms">
        @if(request('category'))
            <div class="flex items-center gap-2 mb-2">
                <span class="text-sm text-[#666666]">Kategori:</span>
                <span class="text-sm font-medium text-rose-600">{{ request('category') }}</span>
                <a href="{{ route('products.index') }}" class="text-xs text-[#999999] hover:text-rose-600">| Hapus</a>
            </div>
        @endif

        @php
            $activeFilters = [
                'new' => request('badge') === 'new',
                'sale' => request('badge') === 'sale',
            ];
        @endphp

        @if($categories && $categories->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mt-3">
                <span class="text-xs text-[#999999]">Urutkan / Filter:</span>
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', array_merge(request()->except(['page','sort']), ['category' => $cat->slug])) }}"
                       class="px-3 py-1 text-xs rounded-full border transition-colors {{ request('category') === $cat->slug ? 'border-rose-500 text-rose-600 bg-rose-50' : 'border-[#E8E0D8] text-[#666666] hover:border-rose-300' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Product Grid --}}
    @if($products->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @else
        <div class="text-center py-16 animate-fade-up">
            <div class="w-20 h-20 mx-auto bg-[#F5F5F5] rounded-full mb-4 flex items-center justify-center">
                <span class="text-3xl">📦</span>
            </div>
            <p class="text-[#999999] font-medium">Tidak ada produk ditemukan.</p>
            <p class="text-sm text-[#CCCCCC] mt-1">Coba ubah filter pencarianmu.</p>
        </div>
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