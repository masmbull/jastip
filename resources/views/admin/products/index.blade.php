@extends('layouts.admin')

@section('title', 'Produk | ' . setting('brand_name'))

@section('content')
<div class="space-y-6" x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }"
     @keydown.escape.window="open = false">

    <x-page-header title="Daftar Produk" subtitle="Kelola semua produk jastip kamu" class="animate-fade-up">
        <x-btn type="button" icon="plus" @click="open = true">Tambah Produk</x-btn>
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
                                    <button type="button" @click="open = true" class="text-[#B45309] font-medium hover:underline">Tambah produk pertama</button>
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

    {{-- Modal Tambah Produk: pakai partial form yang SAMA dengan halaman
         create/edit, jadi tidak ada duplikasi field. --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-[420] flex items-start justify-center overflow-y-auto p-4 sm:p-6"
         role="dialog" aria-modal="true" aria-labelledby="add-product-title">
        <div class="fixed inset-0 bg-black/50" @click="open = false"></div>

        <div class="relative w-full max-w-2xl my-4 sm:my-8 bg-[#FAF6EF] dark:bg-[#1E1E1E] border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[10px] shadow-[6px_6px_0_#141414] dark:shadow-[6px_6px_0_#FAF6EF]"
             @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b-2 border-[#141414] dark:border-[#FAF6EF]">
                <h2 id="add-product-title" class="text-lg font-black text-[#141414] dark:text-[#FAF6EF]">Tambah Produk Baru</h2>
                <button type="button" @click="open = false" aria-label="Tutup" title="Tutup"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-[8px] text-[#141414] dark:text-[#FAF6EF] hover:bg-black/5 dark:hover:bg-white/10">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            @if($errors->any())
                <div class="mx-5 mt-4 border-2 border-[#B91C1C] bg-[#FEE2E2] text-[#7F1D1D] text-sm font-medium rounded-[8px] p-3">
                    <p class="font-black mb-1">Periksa kembali isian berikut:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="px-5 py-4">
                @csrf
                @include('admin.products._form', ['product' => null, 'editing' => false])

                <div class="flex items-center justify-end gap-2 pt-5 mt-5 border-t-2 border-[#141414]/15 dark:border-[#FAF6EF]/15">
                    <button type="button" @click="open = false" class="nb-btn px-4 py-2.5 bg-white dark:bg-[#121212] text-[#141414] dark:text-[#FAF6EF] text-sm">Batal</button>
                    <button type="submit" class="nb-btn px-4 py-2.5 bg-[#FFC800] text-[#141414] text-sm font-black">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection