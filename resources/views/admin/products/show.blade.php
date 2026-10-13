@extends('layouts.admin')
@section('title', 'Detail Produk | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Detail Produk" subtitle="Lihat informasi lengkap produk" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.products.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
        <x-btn as="a" :href="route('admin.products.edit', $product)" icon="pencil">Edit</x-btn>
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
              data-confirm="Hapus produk ini?" class="inline">
            @csrf @method('DELETE')
            <x-btn type="submit" variant="danger" icon="trash">Hapus</x-btn>
        </form>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-up" style="animation-delay: 100ms">
        <x-card>
            <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-4">Gambar Produk</h2>
            <div class="relative">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="w-full rounded-lg object-cover aspect-[4/3]" loading="lazy" decoding="async">
                @if($product->is_featured)
                    <span class="absolute top-3 left-3"><x-badge tone="brand">Unggulan</x-badge></span>
                @endif
            </div>
        </x-card>

        <x-card class="space-y-4">
            <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide">Informasi Produk</h2>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Nama:</span>
                <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->name }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Slug:</span>
                <span class="font-mono text-sm text-[#64748B] dark:text-[#cbd5e1] bg-[#F1F5F9] dark:bg-[#2e323b] rounded px-2 py-0.5">{{ $product->slug }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Kategori:</span>
                <span class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->category ? $product->category->name : '-' }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Harga:</span>
                <span class="font-bold text-orange-600 dark:text-orange-400 num">{{ $product->formatted_price }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Stok:</span>
                <span class="font-medium num @if($product->stock <= 0) text-red-500 dark:text-red-400 @else text-[#1E293B] dark:text-[#f1f5f9] @endif">
                    {{ $product->stock }} {{ $product->unit ?? 'pcs' }}
                </span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">SKU:</span>
                <span class="font-mono text-sm @if($product->sku) text-[#1E293B] dark:text-[#f1f5f9] @else text-[#94A3B8] dark:text-[#64748B] @endif">{{ $product->sku ?? '-' }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Status:</span>
                <x-badge :tone="$product->is_active ? 'success' : 'neutral'">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</x-badge>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[#64748B] dark:text-[#cbd5e1]">Unggulan:</span>
                <x-badge :tone="$product->is_featured ? 'brand' : 'neutral'">{{ $product->is_featured ? 'Ya' : 'Tidak' }}</x-badge>
            </div>
        </x-card>
    </div>

    <x-card class="animate-fade-up" style="animation-delay: 160ms">
        <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-4">Deskripsi</h2>
        <div class="prose porange-sm max-w-none text-[#64748B] dark:text-[#cbd5e1]">
            {!! Str::markdown($product->description) !!}
        </div>
    </x-card>

    <x-card class="animate-fade-up" style="animation-delay: 220ms">
        <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-4">Aksi Cepat</h2>
        <div class="flex flex-wrap gap-3">
            <x-btn as="a" :href="route('admin.products.edit', $product)" icon="pencil">Edit Produk</x-btn>
            <x-btn as="a" :href="route('products.show', $product->slug)" target="_blank" variant="outline" icon="external-link">Lihat di Depan</x-btn>
            <x-btn as="a" :href="\App\Services\WhatsappService::contactUrl('Saya ingin informasi produk ' . $product->name)" target="_blank" variant="outline" icon="chat">Kirim ke WA</x-btn>
        </div>
    </x-card>
</div>
@endsection