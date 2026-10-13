@extends('layouts.admin')
@section('title', 'Edit Produk | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Edit Produk" subtitle="Perbarui detail produk" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.products.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
    </x-page-header>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="max-w-2xl animate-fade-up" style="animation-delay: 120ms">
        @csrf
        @method('PUT')
        <x-card class="space-y-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Kategori *</label>
                    <select name="category_id" required
                            class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Nama Produk *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Harga (Rp) *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" min="1000" required
                               class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('price') border-red-500 @enderror">
                        @error('price') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Set Biaya Fee (Rp)</label>
                        <input type="number" name="setbiaya_fee" value="{{ old('setbiaya_fee', $product->setbiaya_fee ?? 0) }}" min="0"
                               class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('setbiaya_fee') border-red-500 @enderror">
                        <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">0 = pakai fee global dari Pengaturan. Isi angka untuk override per produk.</p>
                        @error('setbiaya_fee') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Stok *</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                               class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('stock') border-red-500 @enderror">
                        @error('stock') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Unit</label>
                        <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'pcs') }}"
                               class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Berat per Unit (kg)</label>
                    <input type="number" step="0.01" name="weight" value="{{ old('weight', $product->weight) }}" min="0" max="100"
                           class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('weight') border-red-500 @enderror"
                           placeholder="Mis. 0.5">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Kosongkan = 0,5 kg/unit (default estimasi ongkir).</p>
                    @error('weight') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                               class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300"
                               placeholder="Opsional">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $product->slug) }}"
                               class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Deskripsi</label>
                    <textarea name="description" rows="4" required
                              class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                           class="w-4 h-4 text-orange-600 border-[#E2E8F0] dark:border-[#404854] rounded">
                    <span class="text-sm text-[#1E293B] dark:text-[#f1f5f9]">Produk unggulan?</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-orange-600 border-[#E2E8F0] dark:border-[#404854] rounded">
                    <span class="text-sm text-[#1E293B] dark:text-[#f1f5f9]">Tampilkan di halaman depan</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_viral" value="1" {{ old('is_viral', $product->is_viral) ? 'checked' : '' }}
                           class="w-4 h-4 text-orange-600 border-[#E2E8F0] dark:border-[#404854] rounded">
                    <span class="text-sm text-[#1E293B] dark:text-[#f1f5f9]">Tandai sebagai produk viral</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Foto Produk</label>
                @if($product->image)
                    <div class="mb-3">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg" loading="lazy" decoding="async">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('image') border-red-500 @enderror">
                @error('image') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
                <x-btn as="a" :href="route('admin.products.index')" variant="ghost">Batal</x-btn>
                <x-btn type="submit">Simpan Perubahan</x-btn>
            </div>
        </x-card>
    </form>
</div>
@endsection