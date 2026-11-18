@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">

    <x-page-header title="Semua Produk" :subtitle="$products->total() . ' produk tersedia'"
                   class="mb-8 animate-fade-up">
        <div>
            <label for="sortSelect" class="sr-only">Urutkan produk</label>
            <select id="sortSelect"
                    class="nb-input px-4 py-2.5 text-sm font-bold text-[#141414] dark:text-[#FAF6EF]">
                <option value="popular" {{ ($sortBy ?? 'popular') === 'popular' ? 'selected' : '' }}>Produk Populer</option>
                <option value="terbaru" {{ ($sortBy ?? '') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="termurah" {{ ($sortBy ?? '') === 'termurah' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="termahal" {{ ($sortBy ?? '') === 'termahal' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </div>
    </x-page-header>

    <div id="filters" class="mb-6 animate-fade-up" style="animation-delay: 120ms">
        @if(request('category'))
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="text-sm font-medium text-[#475569] dark:text-[#A8A29E]">Kategori:</span>
                <x-badge tone="brand">{{ request('category') }}</x-badge>
                <a href="{{ route('products.index') }}"
                   class="text-xs font-bold text-[#475569] dark:text-[#A8A29E] underline underline-offset-2 hover:text-[#141414] dark:hover:text-[#FAF6EF] min-h-[44px] inline-flex items-center">
                    Hapus filter
                </a>
            </div>
        @endif

        @if($categories && $categories->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-black uppercase tracking-wide text-[#475569] dark:text-[#A8A29E] mr-1">Filter:</span>
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', array_merge(request()->except(['page','sort']), ['category' => $cat->slug])) }}"
                       class="inline-flex items-center min-h-[40px] px-3 py-1.5 text-xs font-bold rounded-[8px] border-2 transition-colors {{ request('category') === $cat->slug ? 'border-[#141414] dark:border-[#FAF6EF] bg-[#FFC800] text-[#141414]' : 'border-[#141414]/25 dark:border-[#FAF6EF]/30 text-[#141414] dark:text-[#FAF6EF] hover:border-[#141414] dark:hover:border-[#FAF6EF] hover:bg-[#FFF7D6] dark:hover:bg-[#262626]' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @if($products->isNotEmpty())
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5 nb-rainbow">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @else
        <x-empty-state icon="cube" title="Tidak ada produk ditemukan." class="animate-fade-up nb-card">
            Coba ubah filter atau kata kunci pencarian.
            <div class="mt-4">
                <x-btn as="a" :href="route('products.index')" variant="ghost">Reset filter</x-btn>
            </div>
        </x-empty-state>
    @endif

    @if($products->hasPages())
        <div class="mt-10 md:mt-12">
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
