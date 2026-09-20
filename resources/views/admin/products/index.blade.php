@extends('layouts.admin')

@section('title', 'Produk | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Daftar Produk</h1>
            <p class="text-sm text-[#999999] mt-1">Kelola semua produk jastip kamu</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Produk
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] overflow-hidden animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F8F5F0] text-left text-xs text-[#666666] uppercase">
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0EDE7]">
                    @forelse($products as $product)
                        <tr class="hover:bg-[#fafafa]">
                            <td class="px-4 py-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-14 h-14 rounded-lg object-cover" loading="lazy" decoding="async">
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-[#333333]">{{ $product->name }}</p>
                                <p class="text-xs text-[#999999] font-mono">{{ $product->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-[#333333]">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-rose-600">{{ $product->formatted_price }}</td>
                            <td class="px-4 py-3">
                                @if($product->stock > 0)
                                    <span class="text-green-600 font-medium">{{ $product->stock }}</span>
                                @else
                                    <span class="text-red-500 font-medium">Habis</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="text-[#999999] hover:text-rose-600 p-1" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H11a2 2 0 002-2V6a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.products.destroy', $product->id) }}"
                                          data-confirm="Yakin ingin menghapus produk ini?"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142a2 2 0 01-1.994 1.827H7.864a2 2 0 01-1.994-1.827L5 7M10 11V6a1 1 0 011-1h2a1 1 0 011 1v5m-4 0v9h6v-9m-6 0L8 21h8l-2-9h-4z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 mx-auto bg-[#F5F5F5] rounded-full mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-[#999999]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4v4H5v10h12v4l8-4v-4H5V7"></path>
                                    </svg>
                                </div>
                                <p class="text-[#999999] font-medium">Belum ada produk.</p>
                                <a href="{{ route('admin.products.create') }}"
                                   class="text-sm text-rose-600 font-medium hover:underline">
                                    Tambah produk pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection