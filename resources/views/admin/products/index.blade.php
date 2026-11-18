@extends('layouts.admin')

@section('title', 'Produk | ' . setting('brand_name'))

@section('content')
<div class="space-y-5" x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }"
     @keydown.escape.window="open = false">

    <x-page-header title="Daftar Produk" subtitle="Kelola katalog jastip: stok, harga, dan foto." class="animate-fade-up">
        <x-btn type="button" icon="plus" @click="open = true">Tambah Produk</x-btn>
    </x-page-header>

    <div class="nb-table-shell animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-3 font-black">Foto</th>
                        <th scope="col" class="px-4 py-3 font-black">Nama Produk</th>
                        <th scope="col" class="px-4 py-3 font-black">Kategori</th>
                        <th scope="col" class="px-4 py-3 font-black">Harga</th>
                        <th scope="col" class="px-4 py-3 font-black">Stok</th>
                        <th scope="col" class="px-4 py-3 font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @forelse($products as $product)
                        <tr class="bg-white dark:bg-[#1E1E1E] hover:bg-[#FFFDF0] dark:hover:bg-[#262626] transition-colors">
                            <td class="px-4 py-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="h-14 w-14 rounded-[8px] border-2 border-[#141414] object-cover dark:border-[#FAF6EF]" loading="lazy" decoding="async">
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $product->name }}</p>
                                <p class="font-mono text-xs text-[#475569]">{{ $product->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-[#141414] dark:text-[#FAF6EF]">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm font-black num text-[#141414] dark:text-[#FFC800]">{{ $product->formatted_price }}</td>
                            <td class="px-4 py-3">
                                @if($product->stock > 0)
                                    <span class="font-bold num text-[#141414] dark:text-[#FAF6EF]">{{ $product->stock }}</span>
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
                                            class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-[8px] text-[#475569] transition-colors hover:border-2 hover:border-[#141414] hover:bg-[#FFF7D6] hover:text-[#141414] dark:hover:border-[#FAF6EF]" title="Lihat cepat" aria-label="Lihat cepat">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-[8px] text-[#475569] transition-colors hover:border-2 hover:border-[#141414] hover:bg-[#FFF7D6] hover:text-[#141414] dark:hover:border-[#FAF6EF]" title="Ubah" aria-label="Ubah">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                          data-confirm="Yakin ingin menghapus produk ini?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-[8px] text-[#475569] transition-colors hover:bg-[#FEE2E2] hover:text-[#991B1B]" title="Hapus" aria-label="Hapus">
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
                                    <button type="button" @click="open = true" class="font-bold text-[#141414] underline underline-offset-2 hover:text-[#92400E]">Tambah produk pertama</button>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($products, 'hasPages') && $products->hasPages())
        <div>{{ $products->links() }}</div>
    @endif

    {{-- Modal Tambah Produk: partial form yang sama dengan create/edit. --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-[420] flex items-start justify-center overflow-y-auto p-4 sm:p-6"
         role="dialog" aria-modal="true" aria-labelledby="add-product-title">
        <div class="fixed inset-0 bg-black/50" @click="open = false"></div>

        <div class="relative my-4 w-full max-w-2xl rounded-[10px] border-2 border-[#141414] bg-[#FAF6EF] shadow-[6px_6px_0_#141414] sm:my-8 dark:border-[#FAF6EF] dark:bg-[#1E1E1E] dark:shadow-[6px_6px_0_#FAF6EF]"
             @click.stop>
            <div class="flex items-center justify-between border-b-2 border-[#141414] px-5 py-4 dark:border-[#FAF6EF]">
                <h2 id="add-product-title" class="font-display text-lg font-extrabold text-[#141414] dark:text-[#FAF6EF]">Tambah Produk Baru</h2>
                <button type="button" @click="open = false" aria-label="Tutup" title="Tutup"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[8px] text-[#141414] hover:bg-black/5 dark:text-[#FAF6EF] dark:hover:bg-white/10">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            @if($errors->any())
                <div class="mx-5 mt-4 rounded-[8px] border-2 border-[#B91C1C] bg-[#FEE2E2] p-3 text-sm font-medium text-[#7F1D1D]">
                    <p class="mb-1 font-black">Periksa kembali isian berikut:</p>
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="px-5 py-4">
                @csrf
                @include('admin.products._form', ['product' => null, 'editing' => false])

                <div class="mt-5 flex items-center justify-end gap-2 border-t-2 border-[#141414]/15 pt-5 dark:border-[#FAF6EF]/15">
                    <button type="button" @click="open = false" class="nb-btn bg-white px-4 py-2.5 text-sm text-[#141414] dark:bg-[#121212] dark:text-[#FAF6EF]">Batal</button>
                    <button type="submit" class="nb-btn bg-[#FFC800] px-4 py-2.5 text-sm font-black text-[#141414]">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
