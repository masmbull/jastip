@extends('layouts.admin')

@section('title', 'Laporan Inventori | ' . setting('brand_name'))

@section('content')
<div class="space-y-6">

    <x-page-header title="Laporan Inventori" subtitle="Stok produk diurutkan dari yang paling sedikit" class="animate-fade-up" />

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                        <th class="px-4 py-3 text-right">Terjual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($products as $product)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3 font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-right text-[#1E293B] dark:text-[#f1f5f9] num">Rp {{ number_format((int) $product->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($product->stock > 0)
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium num">{{ $product->stock }}</span>
                                @else
                                    <x-badge tone="danger">Habis</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ $product->sold_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="cube" title="Belum ada produk.">
                                    Produk yang kamu tambahkan akan tampil di sini.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @if($products->hasPages())
        <div>{{ $products->links() }}</div>
    @endif

</div>
@endsection