@extends('layouts.admin')
@section('title', 'Tambah Kategori | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B]">Tambah Kategori</h1>
            <a href="{{ route('admin.categories.index') }}" class="text-sm text-orange-600 hover:underline ml-2">Kembali</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}"
          class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-6 space-y-6 max-w-2xl">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-[#1E293B] mb-1">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('name') border-red-500 @enderror">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') ?: Str::slug(old('name')) }}"
                       placeholder="opsional, otomatis diisi jika dikosongkan"
                    class="w-full px-4 py-3 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                <p class="text-xs text-[#94A3B8] mt-1">Biarkan kosong untuk membuat otomatis dari nama.</p>
                @error('slug') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] mb-1">Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-3 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B] mb-1">Ikon Emoji</label>
                <input type="text" name="icon" value="{{ old('icon') }}"
                    placeholder="📦 atau emoji lain"
                    class="w-full px-4 py-3 border border-[#E2E8F0] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                <p class="text-xs text-[#94A3B8] mt-1">Contoh: 🧴 untuk Parfum, 🥤 untuk Tumbler, dll.</p>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="w-4 h-4 text-orange-600 border-[#E2E8F0] rounded">
                    <span class="text-sm text-[#1E293B]">Tampilkan di halaman depan</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm text-[#94A3B8] hover:text-[#1E293B]">Batal</a>
            <button type="submit" class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection