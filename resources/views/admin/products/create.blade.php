@extends('layouts.admin')
@section('title', 'Tambah Produk | ' . setting('brand_name'))

@php
    $categories = $categories ?? \App\Models\Category::all();
@endphp

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Tambah Produk</h1>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-rose-600 ml-2 hover:underline">
                Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-6 space-y-6 max-w-2xl">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#333333] mb-1">Nama Produk *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300 @error('name') border-red-500 @enderror">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#333333] mb-1">Harga (Rp) *</label>
                <input type="number" name="price" value="{{ old('price') }}" min="1000" required
                    class="w-full px-4 py-3 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300 @error('price') border-red-500 @enderror">
                @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#333333] mb-1">Deskripsi</label>
                <textarea name="description" rows="4" required
                    class="w-full px-4 py-3 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#333333] mb-1">Stok *</label>
                <input type="number" name="stock" value="{{ old('stock') }}" min="0" required
                    class="w-full px-4 py-3 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300 @error('stock') border-red-500 @enderror">
                @error('stock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#333333] mb-1">Unit</label>
                <input type="text" name="unit" value="{{ old('unit') ?: 'pcs' }}"
                    class="w-full px-4 py-3 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                    class="w-4 h-4 text-rose-600 border-[#E8E0D8] rounded">
                <span class="text-sm text-[#333333]">Produk unggulan?</span>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}
                    class="w-4 h-4 text-rose-600 border-[#E8E0D8] rounded">
                <span class="text-sm text-[#333333]">Tampilkan di halaman depan</span>
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-[#333333] mb-1">Foto Produk *</label>
            <input type="file" name="image" accept="image/*" required
                class="w-full px-4 py-3 border border-[#E8E0D8] rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-300 @error('image') border-red-500 @enderror">
            @error('image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E8E0D8]">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm text-[#999999] hover:text-[#333333]">Batal</a>
            <button type="submit" class="px-6 py-2 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-lg shadow-sm">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection