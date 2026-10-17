{{--
  Field form produk (dipakai bareng oleh halaman create, edit, dan modal di
  daftar produk). Sengaja TIDAK memuat <form>: pemanggil yang membungkusnya.

  Variabel:
    $product  : Product|null (null = mode tambah)
    $editing  : bool
--}}
@php
    $editing = $editing ?? false;
    $product = $product ?? null;
    $categories = $categories ?? \App\Models\Category::orderBy('name')->get();
    $selectedCategory = old('category_id', $product->category_id ?? '');
    $isPo = (bool) old('is_po', $product->is_po ?? false);
@endphp

<div class="space-y-5"
     x-data="{ po: {{ $isPo ? 'true' : 'false' }} }">

    <div>
        <label class="block text-sm font-bold mb-1.5" for="p-category">Kategori <span class="text-[#B91C1C]">*</span></label>
        <select id="p-category" name="category_id" required class="nb-input w-full px-3 py-2.5">
            <option value="">Pilih kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected($selectedCategory == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-bold mb-1.5" for="p-name">Nama Produk <span class="text-[#B91C1C]">*</span></label>
        <input id="p-name" type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
               class="nb-input w-full px-3 py-2.5 @error('name') border-[#B91C1C] @enderror">
        @error('name') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold mb-1.5" for="p-price">Harga (Rp) <span class="text-[#B91C1C]">*</span></label>
            <input id="p-price" type="number" name="price" min="0" value="{{ old('price', $product->price ?? '') }}" required
                   class="nb-input w-full px-3 py-2.5 num @error('price') border-[#B91C1C] @enderror">
            @error('price') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-bold mb-1.5" for="p-fee">Set Biaya Fee (Rp)</label>
            <input id="p-fee" type="number" name="setbiaya_fee" min="0" value="{{ old('setbiaya_fee', $product->setbiaya_fee ?? 0) }}"
                   class="nb-input w-full px-3 py-2.5 num">
            <p class="text-xs font-medium text-[#475569] dark:text-[#A8A29E] mt-1">0 = pakai fee global Pengaturan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-bold mb-1.5" for="p-stock">Stok <span class="text-[#B91C1C]">*</span></label>
            <input id="p-stock" type="number" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}" required
                   class="nb-input w-full px-3 py-2.5 num @error('stock') border-[#B91C1C] @enderror">
            @error('stock') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-bold mb-1.5" for="p-unit">Satuan</label>
            <input id="p-unit" type="text" name="unit" value="{{ old('unit', $product->unit ?? 'pcs') }}"
                   class="nb-input w-full px-3 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-bold mb-1.5" for="p-weight">Berat/Unit (kg)</label>
            <input id="p-weight" type="number" step="0.01" min="0" max="100" name="weight"
                   value="{{ old('weight', $product->weight ?? '') }}" placeholder="0.5"
                   class="nb-input w-full px-3 py-2.5 num">
        </div>
    </div>

    <div>
        <label class="block text-sm font-bold mb-1.5" for="p-description">Deskripsi <span class="text-[#B91C1C]">*</span></label>
        <textarea id="p-description" name="description" rows="3" required
                  class="nb-input w-full px-3 py-2.5 @error('description') border-[#B91C1C] @enderror">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Ketersediaan: Ready atau Pre-Order (PO) + estimasi siap --}}
    <fieldset class="border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] p-4">
        <legend class="px-2 text-sm font-black uppercase tracking-wide">Ketersediaan Barang</legend>

        <div class="flex flex-wrap gap-2">
            <label class="cursor-pointer">
                <input type="radio" name="is_po" value="0" class="peer sr-only" x-model="po" :value="false" @checked(! $isPo)>
                <span class="nb-btn inline-flex px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E] text-[#141414] dark:text-[#FAF6EF]"
                      :class="!po ? 'bg-[#FFC800]!' : ''">Ready Stock</span>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="is_po" value="1" class="peer sr-only" x-model="po" :value="true" @checked($isPo)>
                <span class="nb-btn inline-flex px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E] text-[#141414] dark:text-[#FAF6EF]"
                      :class="po ? 'bg-[#FFC800]!' : ''">Pre-Order (PO)</span>
            </label>
        </div>

        <div x-show="po" x-cloak class="mt-4">
            <label class="block text-sm font-bold mb-1.5" for="p-ready">Estimasi Barang Siap</label>
            <input id="p-ready" type="date" name="ready_date"
                   value="{{ old('ready_date', optional($product->ready_date ?? null)->format('Y-m-d')) }}"
                   class="nb-input w-full px-3 py-2.5">
            <p class="text-xs font-medium text-[#475569] dark:text-[#A8A29E] mt-1">Muncul ke pembeli sebagai "Estimasi siap: &lt;tanggal&gt;".</p>
            @error('ready_date') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
        </div>
    </fieldset>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold mb-1.5" for="p-sku">SKU</label>
            <input id="p-sku" type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" placeholder="Opsional"
                   class="nb-input w-full px-3 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-bold mb-1.5" for="p-slug">Slug</label>
            <input id="p-slug" type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}" placeholder="otomatis dari nama"
                   class="nb-input w-full px-3 py-2.5">
        </div>
    </div>

    <div class="space-y-2.5">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))
                   class="w-4 h-4 rounded-[4px] border-2 border-[#141414] text-[#141414]">
            <span class="text-sm font-medium">Produk unggulan (tampil di beranda)</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_viral" value="1" @checked(old('is_viral', $product->is_viral ?? false))
                   class="w-4 h-4 rounded-[4px] border-2 border-[#141414] text-[#141414]">
            <span class="text-sm font-medium">Tandai sebagai produk viral</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))
                   class="w-4 h-4 rounded-[4px] border-2 border-[#141414] text-[#141414]">
            <span class="text-sm font-medium">Aktif / tampilkan di katalog</span>
        </label>
    </div>

    <div>
        <label class="block text-sm font-bold mb-1.5" for="p-image">Foto Produk {{ $editing ? '(biarkan kosong jika tidak ganti)' : '*' }}</label>
        @if($editing && $product?->image)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded-[10px] border-2 border-[#141414] mb-2" loading="lazy" decoding="async">
        @endif
        <input id="p-image" type="file" name="image" accept="image/*" @required(! $editing)
               class="nb-input w-full px-3 py-2.5 text-sm @error('image') border-[#B91C1C] @enderror">
        @error('image') <p class="text-xs font-bold text-[#991B1B] mt-1">{{ $message }}</p> @enderror
    </div>
</div>