@extends('layouts.admin')

@section('title', 'Produk | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">

    <x-page-header title="Daftar Produk" subtitle="Kelola semua produk jastip kamu" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.products.create')" icon="plus">Tambah Produk</x-btn>
    </x-page-header>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Foto</th>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($products as $product)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-14 h-14 rounded-lg object-cover" loading="lazy" decoding="async">
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->name }}</p>
                                <p class="text-xs text-[#94A3B8] dark:text-[#64748B] font-mono">{{ $product->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-orange-600 dark:text-orange-400 num">{{ $product->formatted_price }}</td>
                            <td class="px-4 py-3">
                                @if($product->stock > 0)
                                    <span class="text-green-600 dark:text-green-400 font-medium num">{{ $product->stock }}</span>
                                @else
                                    <x-badge tone="danger">Habis</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                            data-qv-url="{{ route('admin.products.quickview', $product) }}"
                                            data-qv-title="{{ $product->name }}"
                                            @click="$dispatch('quickview', { url: $el.dataset.qvUrl, title: $el.dataset.qvTitle })"
                                            class="text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors" title="Lihat cepat" aria-label="Lihat cepat">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors" title="Ubah" aria-label="Ubah">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                          data-confirm="Yakin ingin menghapus produk ini?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-[#64748B] dark:text-[#cbd5e1] hover:text-red-600 dark:hover:text-red-400 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Hapus" aria-label="Hapus">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="cube" title="Belum ada produk.">
                                    <a href="{{ route('admin.products.create') }}" class="text-orange-600 dark:text-orange-400 font-medium hover:underline">Tambah produk pertama</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @if(method_exists($products, 'hasPages') && $products->hasPages())
        <div>{{ $products->links() }}</div>
    @endif

</div>
@endsection