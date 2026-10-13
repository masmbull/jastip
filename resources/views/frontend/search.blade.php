@extends('layouts.app')

@section('title', 'Cari Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Search Bar --}}
    <div class="mb-8">
        <form method="GET" action="{{ route('search') }}" class="flex flex-col sm:flex-row gap-2" role="search">
            <label for="search-q" class="sr-only">Cari produk</label>
            <input type="text" id="search-q" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                   class="flex-1 px-4 py-3 bg-white dark:bg-[#23252b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] placeholder-[#94A3B8] dark:placeholder-[#64748B] focus:outline-none focus:ring-2 focus:ring-orange-300">
            <x-btn type="submit" variant="brand" icon="search" class="px-6 py-3">
                Cari
            </x-btn>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Sidebar Filters --}}
        <div class="lg:col-span-1">
            <x-card class="lg:sticky lg:top-24">
                <h2 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Filter</h2>

                <form method="GET" action="{{ route('search') }}" class="space-y-5">
                    {{-- Search --}}
                    <div>
                        <label for="filter-q" class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">Pencarian</label>
                        <input type="text" id="filter-q" name="q" value="{{ request('q') }}"
                               class="w-full mt-2 px-3 py-2 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="filter-category" class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">Kategori</label>
                        <select id="filter-category" name="category" class="w-full mt-2 px-3 py-2 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Price Range --}}
                    <fieldset>
                        <legend class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-3">Harga</legend>
                        <div class="space-y-2">
                            <label for="filter-price-min" class="sr-only">Harga minimum</label>
                            <input type="number" id="filter-price-min" name="price_min" value="{{ request('price_min') }}" placeholder="Min" step="10000"
                                   class="w-full px-3 py-2 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <label for="filter-price-max" class="sr-only">Harga maksimum</label>
                            <input type="number" id="filter-price-max" name="price_max" value="{{ request('price_max') }}" placeholder="Max" step="10000"
                                   class="w-full px-3 py-2 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                    </fieldset>

                    {{-- Stock Filter --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="in_stock" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}
                               class="w-5 h-5 rounded cursor-pointer accent-orange-500">
                        <label for="in_stock" class="text-sm text-[#1E293B] dark:text-[#f1f5f9] cursor-pointer">
                            Stok Tersedia
                        </label>
                    </div>

                    {{-- Featured Filter --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="featured" name="featured" value="1" {{ request('featured') ? 'checked' : '' }}
                               class="w-5 h-5 rounded cursor-pointer accent-orange-500">
                        <label for="featured" class="text-sm text-[#1E293B] dark:text-[#f1f5f9] cursor-pointer">
                            Produk Unggulan
                        </label>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label for="filter-sort" class="text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9]">Urutkan</label>
                        <select id="filter-sort" name="sort" class="w-full mt-2 px-3 py-2 bg-white dark:bg-[#2e323b] border border-[#E2E8F0] dark:border-[#404854] rounded-lg text-[#1E293B] dark:text-[#f1f5f9] text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="space-y-2">
                        <x-btn type="submit" variant="brand" class="w-full">
                            Terapkan Filter
                        </x-btn>
                        <x-btn as="a" :href="route('search')" variant="outline" class="w-full">
                            Reset
                        </x-btn>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Products Grid --}}
        <div class="lg:col-span-3">
            {{-- Results Info --}}
            <div class="mb-6">
                <p class="text-[#64748B] dark:text-[#cbd5e1]">
                    Menampilkan <strong class="num">{{ $products->count() }}</strong> dari <strong class="num">{{ $products->total() }}</strong> produk
                </p>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                <div class="mt-12">
                    {{ $products->links('components.pagination') }}
                </div>
                @endif
            @else
                <x-empty-state icon="search" title="Produk Tidak Ditemukan">
                    Coba ubah filter atau pencarianmu.
                    <div class="mt-4">
                        <x-btn as="a" :href="route('search')" variant="brand" class="rounded-full">
                            Reset Filter
                        </x-btn>
                    </div>
                </x-empty-state>
            @endif
        </div>
    </div>
</div>
@endsection