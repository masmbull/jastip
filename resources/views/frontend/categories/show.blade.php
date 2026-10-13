@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8">
        <nav aria-label="Breadcrumb" class="text-sm text-[#64748B] dark:text-[#cbd5e1] mb-3">
            <ol class="flex flex-wrap items-center gap-1">
                <li><a href="{{ route('categories.index') }}" class="hover:text-orange-600 dark:hover:text-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">Kategori</a></li>
                <li aria-hidden="true" class="mx-1">/</li>
                <li class="text-[#1E293B] dark:text-[#f1f5f9]" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-2">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-[#64748B] dark:text-[#cbd5e1]">{{ $category->description }}</p>
        @endif
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @forelse($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-span-full">
                <x-empty-state icon="search" title="Produk tidak ditemukan di kategori ini." />
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-12">
            {{ $products->links('components.pagination') }}
        </div>
    @endif
</div>
@endsection