@extends('layouts.admin')
@section('title', 'Edit Produk | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Edit Produk" subtitle="Perbarui detail produk" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.products.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
    </x-page-header>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
          class="max-w-2xl animate-fade-up" style="animation-delay: 120ms">
        @csrf
        @method('PUT')
        <x-card>
            @include('admin.products._form', ['product' => $product, 'editing' => true])

            <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t-2 border-[#141414] dark:border-[#FAF6EF]">
                <x-btn as="a" :href="route('admin.products.index')" variant="ghost">Batal</x-btn>
                <x-btn type="submit">Simpan Perubahan</x-btn>
            </div>
        </x-card>
    </form>
</div>
@endsection