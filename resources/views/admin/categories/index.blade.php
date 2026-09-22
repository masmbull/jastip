@extends('layouts.admin')

@section('title', 'Kategori | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B]">Daftar Kategori</h1>
            <p class="text-sm text-[#94A3B8] mt-1">Kelola kategori produk</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Kategori
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] overflow-hidden animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F1F5F9] text-left text-xs text-[#64748B] uppercase">
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($categories as $category)
                        <tr class="hover:bg-[#f8fafc]">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center text-lg">
                                        {{ $category->icon ?? '📦' }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#1E293B]">{{ $category->name }}</p>
                                        <p class="text-xs font-mono text-[#94A3B8]">{{ $category->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $category->products_count }} produk</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full @if($category->is_active) bg-green-100 text-green-700 @else bg-gray-100 text-gray-500 @endif">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="text-[#94A3B8] hover:text-orange-600 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H11a2 2 0 002-2V6a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST"
                                                                                    action="{{ route('admin.categories.destroy', $category) }}"
                                          data-confirm="Hapus kategori ini?"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-500 hover:text-red-700 p-1">
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
                            <td colspan="4" class="px-4 py-12 text-center">
                                <p class="mb-2 font-medium text-[#94A3B8]">Belum ada kategori.</p>
                                <a href="{{ route('admin.categories.create') }}"
                                   class="text-sm text-orange-600 font-medium hover:underline">
                                    Tambah kategori pertama
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