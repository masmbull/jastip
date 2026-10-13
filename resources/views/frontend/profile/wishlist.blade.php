@extends('layouts.app')

@section('title', 'Wishlist Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-8">Wishlist Saya</h1>

        @if($wishlists->isEmpty())
        <x-card>
            <x-empty-state icon="heart" title="Wishlist masih kosong">
                Simpan produk incaran kamu supaya mudah ditemukan lagi.
                <div class="mt-4">
                    <x-btn as="a" :href="route('products.index')" variant="brand" icon="bag">Jelajahi Produk</x-btn>
                </div>
            </x-empty-state>
        </x-card>
        @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($wishlists as $wishlist)
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm overflow-hidden border border-[#E2E8F0] dark:border-[#404854] group transition-colors">
                <a href="{{ route('products.show', $wishlist->product->slug) }}" class="block relative aspect-[4/3] overflow-hidden focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <img src="{{ $wishlist->product->image_url }}" alt="{{ $wishlist->product->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                </a>
                <div class="p-4">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase font-medium">{{ $wishlist->product->category->name ?? 'Lainnya' }}</p>
                    <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] line-clamp-1 mb-1">{{ $wishlist->product->name }}</h3>
                    <p class="text-[#0E7490] dark:text-[#67e8f9] font-bold mb-3 num">{{ $wishlist->product->formatted_price }}</p>
                    <button type="button"
                            data-wishlist-toggle
                            data-url="{{ route('wishlist.toggle', ['product' => $wishlist->product->id]) }}"
                            data-wishlisted="1"
                            aria-label="Hapus {{ $wishlist->product->name }} dari wishlist"
                            class="w-full min-h-[44px] px-4 py-2 border-2 border-red-400 text-red-600 dark:text-red-300 font-medium rounded-full text-sm hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                        Hapus dari Wishlist
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        @if($wishlists->hasPages())
        <div class="mt-8">{{ $wishlists->links() }}</div>
        @endif
        @endif
    </div>
</div>
@endsection