@extends('layouts.admin')

@section('title', 'Kategori | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">
    <x-page-header title="Daftar Kategori" subtitle="Kelola kategori produk" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.categories.create')" icon="plus">Tambah Kategori</x-btn>
    </x-page-header>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse($categories as $category)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-500/20 rounded-full flex items-center justify-center text-lg">
                                        {{ $category->icon ?: '🗂️' }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $category->name }}</p>
                                        <p class="text-xs font-mono text-[#94A3B8] dark:text-[#64748B]">{{ $category->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9] num">{{ $category->products_count }} produk</td>
                            <td class="px-4 py-3">
                                <x-badge :tone="$category->is_active ? 'success' : 'neutral'">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors" title="Ubah" aria-label="Ubah">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                          data-confirm="Hapus kategori ini?" class="inline">
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
                            <td colspan="4">
                                <x-empty-state icon="tag" title="Belum ada kategori.">
                                    <a href="{{ route('admin.categories.create') }}" class="text-orange-600 dark:text-orange-400 font-medium hover:underline">Tambah kategori pertama</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection