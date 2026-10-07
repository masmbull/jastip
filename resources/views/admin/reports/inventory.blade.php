@extends('layouts.admin')

@section('title', 'Laporan Inventori | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Laporan Inventori</h1>
        <p class="text-sm text-[#94A3B8] dark:text-[#cbd5e1] mt-1">Stok produk diurutkan dari yang paling sedikit</p>
    </div>

    <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Terjual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @forelse($products as $product)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3 font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">Rp {{ number_format((int) $product->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @if($product->stock > 0)
                                    <span class="text-green-600 dark:text-green-400 font-medium">{{ $product->stock }}</span>
                                @else
                                    <span class="text-red-500 dark:text-red-400 font-medium">Habis</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->sold_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-[#94A3B8]">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->hasPages())
        <div>{{ $products->links() }}</div>
    @endif
</div>
@endsection