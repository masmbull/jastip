@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-[#333333] mb-2">Kategori Produk</h1>
        <p class="text-[#999999]">Pilih kategori favoritmu</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            <x-category-card :category="$category" />
        @empty
            <div class="col-span-full text-center py-16">
                <p class="text-[#999999]">Belum ada kategori.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
