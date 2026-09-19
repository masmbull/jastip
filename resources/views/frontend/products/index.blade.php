@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-[#333333] mb-2">Semua Produk</h1>
        <p class="text-[#999999]">Scroll boleh, checkout belakangan 😆</p>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('products.index') }}" class="mb-8">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari produk..."
                       class="w-full pl-10 pr-4 py-3 border border-[#E8E0D8] rounded-full focus:outline-none focus:ring-2 focus:ring-rose-300 bg-white">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-[#999999]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                </svg>
            </div>
            <button type="submit"
                    class="px-6 py-3 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-full transition-colors">
                Cari
            </button>
        </div>
    </form>

    {{-- Category Filter --}}
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ request()->url() }}"
           class="px-4 py-2 text-sm rounded-full {{ !request('category') ? 'bg-rose-500 text-white' : 'bg-white text-[#666666] hover:bg-rose-50' }} border border-[#E8E0D8] transition-colors">
            Semua
        </a>
        @foreach($categories as $cat)
            <a href="{{ request()->url() }}?category={{ $cat->slug }}"
               class="px-4 py-2 text-sm rounded-full {{ request('category') == $cat->slug ? 'bg-rose-500 text-white' : 'bg-white text-[#666666] hover:bg-rose-50' }} border border-[#E8E0D8] transition-colors">
                {{ $cat->name }} ({{ $cat->products_count }})
            </a>
        @endforeach
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-span-full text-center py-16">
                <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full mb-4 flex items-center justify-center">
                    <span class="text-3xl">🔍</span>
                </div>
                <p class="text-[#999999] mb-2">Produk tidak ditemukan.</p>
                <p class="text-sm text-[#CCCCCC]">Coba kata kunci lain atau cari di kategori yang berbeda.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection