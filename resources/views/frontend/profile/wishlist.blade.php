@extends('layouts.app')

@section('title', 'Wishlist Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-8">Wishlist Saya</h1>

        @if($wishlists->isEmpty())
        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-12 text-center border border-gray-200 dark:border-[#404854]">
            <p class="text-gray-500 dark:text-[#9ca3af] text-lg mb-4">Wishlist masih kosong</p>
            <a href="{{ route('products.index') }}"
               class="inline-block px-6 py-2 bg-[#06B6D4] text-white rounded-lg hover:bg-[#0E7490] transition">Jelajahi Produk</a>
        </div>
        @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($wishlists as $wishlist)
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-[#404854] group">
                <a href="{{ route('products.show', $wishlist->product->slug) }}" class="block relative aspect-[4/3] overflow-hidden">
                    <img src="{{ $wishlist->product->image_url }}" alt="{{ $wishlist->product->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                </a>
                <div class="p-4">
                    <p class="text-xs text-[#94A3B8] uppercase font-medium">{{ $wishlist->product->category->name ?? 'Lainnya' }}</p>
                    <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] line-clamp-1 mb-1">{{ $wishlist->product->name }}</h3>
                    <p class="text-orange-600 font-bold mb-3">{{ $wishlist->product->formatted_price }}</p>
                    <button type="button"
                            data-wishlist-toggle
                            data-url="{{ route('wishlist.toggle', ['product' => $wishlist->product->id]) }}"
                            data-wishlisted="1"
                            class="w-full px-4 py-2 border-2 border-red-400 text-red-500 font-medium rounded-full text-sm hover:bg-red-50 transition-colors">
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
