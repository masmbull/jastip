@extends('layouts.admin')

@section('title', 'Import Produk | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Import Produk" subtitle="Unggah file CSV untuk menambahkan atau memperbarui produk secara massal" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.products.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
    </x-page-header>

    <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data" class="max-w-2xl animate-fade-up" style="animation-delay: 120ms">
        @csrf
        <x-card class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">File CSV *</label>
                <input type="file" name="file" accept=".csv,.txt" required
                       class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('file') border-red-500 @enderror">
                @error('file') <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="bg-[#F1F5F9] dark:bg-[#2e323b] rounded-lg p-4 text-sm text-[#64748B] dark:text-[#cbd5e1]">
                <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Format kolom yang diharapkan:</p>
                <code class="block font-mono text-xs bg-white dark:bg-[#23252b] rounded p-2 border border-[#E2E8F0] dark:border-[#404854] dark:text-[#cbd5e1]">sku,name,description,price,stock,category_id</code>
                <p class="mt-2 text-xs">Produk dengan <code class="font-mono">sku</code> yang sama akan diperbarui (updateOrCreate).</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
                <x-btn as="a" :href="route('admin.products.index')" variant="ghost">Batal</x-btn>
                <x-btn type="submit" icon="download">Import</x-btn>
            </div>
        </x-card>
    </form>
</div>
@endsection