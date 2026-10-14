@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <x-page-header title="Kategori Produk" subtitle="Pilih kategori favoritmu" class="mb-8" />

    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 nb-rainbow">
        @forelse($categories as $category)
            <x-category-card :category="$category" />
        @empty
            <div class="col-span-full">
                <x-empty-state icon="tag" title="Belum ada kategori." />
            </div>
        @endforelse
    </div>
</div>
@endsection