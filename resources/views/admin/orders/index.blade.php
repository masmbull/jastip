@extends('layouts.admin')
@section('title', 'Pesanan | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Daftar Pesanan</h1>
            <p class="text-sm text-[#999999] mt-1">Kelola semua pesanan jastip</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
               class="px-4 py-2 text-sm bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 font-medium">
                Pending
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="px-4 py-2 text-sm bg-[#F0EDE7] text-[#666666] rounded-lg hover:bg-[#E8E0D8] font-medium">
                Semua Pesanan
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] overflow-hidden animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F8F5F0] text-left text-xs text-[#666666] uppercase">
                        <th class="px-4 py-3">No. Pesanan</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Subtotal</th>
                        <th class="px-4 py-3">Ongkir</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0EDE7]">
                    @forelse($orders as $order)
                        <tr class="hover:bg-[#fafafa]">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm text-rose-600">#{{ $order->no }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <p class="font-medium text-[#333333]">{{ $order->customer_name }}</p>
                                <p class="text-xs text-[#999999]">{{ $order->customer_whatsapp }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-[#999999]">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-[#333333]">{{ format_price($order->subtotal) }}</td>
                            <td class="px-4 py-3 text-sm text-[#333333]">{{ format_price($order->shipping_cost) }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-[#333333]">{{ format_price($order->total) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->status_badge_class }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                                                <div class="flex items-center space-x-1">
                                    <button type="button"
                                            data-qv-url="{{ route('admin.orders.quickview', $order) }}"
                                            data-qv-title="#{{ $order->no }}"
                                            @click="$dispatch('quickview', {url:$el.dataset.qvUrl, title:$el.dataset.qvTitle})"
                                            class="text-rose-600 hover:text-rose-800 p-1 rounded-lg hover:bg-[#F5F5F0]" title="Quick view">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s7.5-7.5 10.25-7.5A10 10 0 0 118 8m-1.5 6h.01M9 21a9 9 0 0 1-6.75-3 9 9 0 0 10-6.75"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="text-rose-600 hover:text-rose-800 text-sm font-medium">
                                        Lihat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 mx-auto bg-[#F5F5F5] rounded-full mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-[#999999]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path>
                                    </svg>
                                </div>
                                <p class="text-[#999999] font-medium">Belum ada pesanan.</p>
                                <p class="text-sm text-[#CCCCCC] mt-1">Order akan muncul di sini setelah pelanggan checkout.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection