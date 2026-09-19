@extends('layouts.admin')
@section('title', 'Detail Produk | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Detail Produk</h1>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-rose-600 hover:underline ml-2">Kembali</a>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.edit', $product->id) }}"
               class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium rounded-lg shadow-sm">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}"
                  onsubmit="return confirm('Hapus produk ini?')" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg shadow-sm">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-6">
            <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide mb-4">Gambar Produk</h2>
            <div class="relative">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="w-full rounded-lg object-cover aspect-[4/3]">
                @if($product->is_featured)
                    <span class="absolute top-3 left-3 bg-rose-500 text-white text-xs font-bold px-2 py-1 rounded-full">Unggulan</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-6 space-y-4">
            <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide">Informasi Produk</h2>
            <div>
                <span class="text-[#999999]">Nama:</span>
                <span class="font-medium text-[#333333]">{{ $product->name }}</span>
            </div>
            <div>
                <span class="text-[#999999]">Slug:</span>
                <span class="font-mono text-sm text-[#666666] bg-[#F5F5F5] rounded px-2 py-0.5">{{ $product->slug }}</span>
            </div>
            <div>
                <span class="text-[#999999]">Kategori:</span>
                <span class="text-[#333333]">{{ $product->category ? $product->category->name : '-' }}</span>
            </div>
            <div>
                <span class="text-[#999999]">Harga:</span>
                <span class="font-bold text-rose-600">{{ $product->formatted_price }}</span>
            </div>
            <div>
                <span class="text-[#999999]">Stok:</span>
                <span class="font-medium @if($product->stock <= 0) text-red-500 @else text-[#333333] @endif">
                    {{ $product->stock }} {{ $product->unit ?? 'pcs' }}
                </span>
            </div>
            <div>
                <span class="text-[#999999]">SKU:</span>
                <span class="font-mono text-sm @if($product->sku) text-[#333333] @else text-[#999999] @endif">{{ $product->sku ?? '-' }}</span>
            </div>
            <div>
                <span class="text-[#999999]">Status:</span>
                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-{{ $product->is_active ? 'green' : 'gray' }}-100 text-{{ $product->is_active ? 'green' : 'gray' }}-700">
                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div>
                <span class="text-[#999999]">Unggulan:</span>
                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-{{ $product->is_featured ? 'rose' : 'gray' }}-100 text-{{ $product->is_featured ? 'rose-600' : 'gray-600' }}">
                    {{ $product->is_featured ? 'Ya' : 'Tidak' }}
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-6">
        <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide mb-4">Deskripsi</h2>
        <div class="prose prose-sm max-w-none text-[#666666]">
            {!! Str::markdown($product->description) !!}
        </div>
    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-6">
        <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide mb-4">Aksi Cepat</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.products.edit', $product->id) }}"
               class="inline-flex items-center px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium rounded-lg shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H11a2 2 0 002-2V6a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Edit Produk
            </a>
            <a href="{{ route('products.show', $product->slug) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-[#333333] hover:bg-[#444444] text-white text-sm font-medium rounded-lg shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Lihat di Depan
            </a>
            <a href="{{ \App\Services\WhatsappService::contactUrl('Saya ingin informasi produk ' . $product->name) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.48A11.94 11.94 0 0012 0a11.96 11.96 0 00-8.44 20.73l-2.53 7.55 7.67-2.08A11.88 11.88 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.21-3.48-8.42z" /></svg>
                Kirim ke WA
            </a>
        </div>
    </div>
</div>
@endsection