@extends('layouts.admin')

@section('title', 'Import Produk | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Import Produk</h1>
        <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Unggah file CSV untuk menambahkan atau memperbarui produk secara massal</p>
    </div>

    <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data"
          class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-6 space-y-6 max-w-2xl">
        @csrf

        <div>
            <label class="block text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-1">File CSV *</label>
            <input type="file" name="file" accept=".csv,.txt" required
                class="w-full px-4 py-3 border border-[#E2E8F0] dark:border-[#404854] dark:bg-[#2e323b] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 @error('file') border-red-500 @enderror">
            @error('file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="bg-[#F1F5F9] dark:bg-[#2e323b] rounded-lg p-4 text-sm text-[#64748B] dark:text-[#cbd5e1]">
            <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9] mb-2">Format kolom yang diharapkan:</p>
            <code class="block font-mono text-xs bg-white dark:bg-[#23252b] rounded p-2 border border-[#E2E8F0] dark:border-[#404854]">sku,name,description,price,stock,category_id</code>
            <p class="mt-2 text-xs">Produk dengan <code class="font-mono">sku</code> yang sama akan diperbarui (updateOrCreate).</p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm text-[#94A3B8] hover:text-[#1E293B] dark:hover:text-[#f1f5f9]">Batal</a>
            <button type="submit" class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm">Import</button>
        </div>
    </form>
</div>
@endsection