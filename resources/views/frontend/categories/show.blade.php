@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8">
        <nav class="text-sm text-[#999999] mb-3">
            <a href="{{ route('categories.index') }}" class="hover:text-rose-600">Kategori</a>
            <span class="mx-2">/</span>
            <span class="text-[#333333]">{{ $category->name }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-bold text-[#333333] mb-2">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-[#999999]">{{ $category->description }}</p>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-span-full text-center py-16">
                <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full mb-4 flex items-center justify-center">
                    <span class="text-3xl">🔍</span>
                </div>
                <p class="text-[#999999] mb-2">Produk tidak ditemukan di kategori ini.</p>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
