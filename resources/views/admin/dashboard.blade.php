@extends('layouts.admin')

@section('title', 'Dashboard Admin | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">

    {{-- Welcome + CTA --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Selamat Datang, Admin</h1>
            <p class="text-sm text-[#999999] mt-1">{{ setting('brand_owner', 'Nabila Adriyana') }} — {{ setting('brand_name', 'NITIP DI END') }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Produk
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-up" style="animation-delay: 120ms">

        <div class="bg-white rounded-xl shadow-sm p-5 border border-[#E8E0D8]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4v4H5v10h12v4l8-4v-4H5V7"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-rose-600 font-medium">Detail</a>
            </div>
            <p class="text-3xl font-bold text-[#333333]">
                <span x-data="JDnum({{ $stats['products_count'] ?? 0 }})" x-init="start()" x-text="n">{{ $stats['products_count'] ?? 0 }}</span>
            </p>
            <p class="text-sm text-[#999999]">Total Produk</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-[#E8E0D8]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6a2 2 0 11-4 0 2 2 0 014 0zM9 13h6m-6 4h6m-6 4h6"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="text-xs text-purple-600 font-medium">Detail</a>
            </div>
            <p class="text-3xl font-bold text-[#333333]">
                <span x-data="JDnum({{ $stats['categories_count'] ?? 0 }})" x-init="start()" x-text="n">{{ $stats['categories_count'] ?? 0 }}</span>
            </p>
            <p class="text-sm text-[#999999]">Total Kategori</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-[#E8E0D8]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-green-600 font-medium">Detail</a>
            </div>
            <p class="text-3xl font-bold text-[#333333]">
                <span x-data="JDnum({{ $stats['orders_count'] ?? 0 }})" x-init="start()" x-text="n">{{ $stats['orders_count'] ?? 0 }}</span>
            </p>
            <p class="text-sm text-[#999999]">Total Pesanan</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-[#E8E0D8]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-xs text-amber-600 font-medium">Detail</a>
            </div>
            <p class="text-3xl font-bold text-[#333333]">
                <span x-data="JDnum({{ $stats['orders_pending'] ?? 0 }})" x-init="start()" x-text="n">{{ $stats['orders_pending'] ?? 0 }}</span>
            </p>
            <p class="text-sm text-[#999999]"> Menunggu Konfirmasi</p>
        </div>

    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] overflow-hidden animate-fade-up" style="animation-delay: 240ms">
        <div class="px-6 py-4 border-b border-[#E8E0D8] flex items-center justify-between">
            <h2 class="text-lg font-semibold text-[#333333]">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-rose-600 font-medium">
                Lihat Semua
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F8F5F0] text-left text-xs text-[#666666] uppercase">
                        <th class="px-4 py-3">No. Pesanan</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0EDE7]">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-[#fafafa]">
                            <td class="px-4 py-3 font-mono text-sm text-rose-600">#{{ $order->no }}</td>
                            <td class="px-4 py-3 text-sm text-[#333333]">{{ $order->customer_name }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-[#333333]">{{ format_price($order->subtotal + $order->shipping_cost) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->status_badge_class }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-[#999999]">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[#999999]">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection