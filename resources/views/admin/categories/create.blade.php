@extends('layouts.admin')
@section('title', 'Tambah Kategori | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Tambah Kategori" subtitle="Buat kategori produk baru" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.categories.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
    </x-page-header>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="max-w-2xl animate-fade-up" style="animation-delay: 120ms">
        @csrf
        <x-card class="space-y-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Nama Kategori *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') ?: Str::slug(old('name')) }}"
                           placeholder="opsional, otomatis diisi jika dikosongkan"
                           class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Biarkan kosong untuk membuat otomatis dari nama.</p>
                    @error('slug') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Deskripsi</label>
                    <textarea name="description" rows="2"
                              class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">Ikon Emoji</label>
                    <input type="text" name="icon" value="{{ old('icon') }}"
                           placeholder="Contoh: 🧴"
                           class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mt-1">Masukkan emoji, mis. 🧴 Parfum, 🥤 Tumbler, ⌚ Aksesori.</p>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked
                           class="w-4 h-4 text-orange-600 border-[#E2E8F0] dark:border-[#404854] rounded">
                    <span class="text-sm text-[#1E293B] dark:text-[#f1f5f9]">Tampilkan di halaman depan</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
                <x-btn as="a" :href="route('admin.categories.index')" variant="ghost">Batal</x-btn>
                <x-btn type="submit">Simpan</x-btn>
            </div>
        </x-card>
    </form>
</div>
@endsection