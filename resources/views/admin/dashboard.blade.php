@extends('layouts.admin')

@section('title', 'Dashboard Admin | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">

    {{-- Welcome + CTA --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B]">Selamat Datang, Admin</h1>
            <p class="text-sm text-[#94A3B8] mt-1">{{ setting('brand_owner', 'Nabila Adriyana') }} — {{ setting('brand_name', 'NITIP DI END') }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Produk
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-up" style="animation-delay: 120ms">

        <div class="bg-white rounded-xl shadow-sm p-5 border border-[#E2E8F0]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-orange-600 font-medium">Detail</a>
            </div>
            <p class="text-3xl font-bold text-[#1E293B]">
                <span x-data="JDnum({{ $stats['products_count'] ?? 0 }})" x-init="start()" x-text="n">{{ $stats['products_count'] ?? 0 }}</span>
            </p>
            <p class="text-sm text-[#94A3B8]">Total Produk</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-[#E2E8F0]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3Z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="text-xs text-orange-600 font-medium">Detail</a>
            </div>
            <p class="text-3xl font-bold text-[#1E293B]">
                <span x-data="JDnum({{ $stats['categories_count'] ?? 0 }})" x-init="start()" x-text="n">{{ $stats['categories_count'] ?? 0 }}</span>
            </p>
            <p class="text-sm text-[#94A3B8]">Total Kategori</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-[#E2E8F0]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0Zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0Z"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-orange-600 font-medium">Detail</a>
            </div>
            <p class="text-3xl font-bold text-[#1E293B]">
                <span x-data="JDnum({{ $stats['orders_count'] ?? 0 }})" x-init="start()" x-text="n">{{ $stats['orders_count'] ?? 0 }}</span>
            </p>
            <p class="text-sm text-[#94A3B8]">Total Pesanan</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-[#E2E8F0]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-xs text-orange-600 font-medium">Detail</a>
            </div>
            <p class="text-3xl font-bold text-[#1E293B]">
                <span x-data="JDnum({{ $stats['orders_pending'] ?? 0 }})" x-init="start()" x-text="n">{{ $stats['orders_pending'] ?? 0 }}</span>
            </p>
            <p class="text-sm text-[#94A3B8]"> Menunggu Konfirmasi</p>
        </div>

    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] overflow-hidden animate-fade-up" style="animation-delay: 240ms">
        <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between">
            <h2 class="text-lg font-semibold text-[#1E293B]">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-orange-600 font-medium">
                Lihat Semua
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F1F5F9] text-left text-xs text-[#64748B] uppercase">
                        <th class="px-4 py-3">No. Pesanan</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-[#f8fafc]">
                            <td class="px-4 py-3 font-mono text-sm text-orange-600">#{{ $order->no }}</td>
                            <td class="px-4 py-3 text-sm text-[#1E293B]">{{ $order->customer_name }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-[#1E293B]">{{ format_price($order->subtotal + $order->shipping_cost) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->status_badge_class }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-[#94A3B8]">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[#94A3B8]">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection