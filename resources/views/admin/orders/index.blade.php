@extends('layouts.admin')
@section('title', 'Pesanan | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B]">Daftar Pesanan</h1>
            <p class="text-sm text-[#94A3B8] mt-1">Kelola semua pesanan jastip</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
               class="px-4 py-2 text-sm bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 font-medium">
                Pending
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="px-4 py-2 text-sm bg-[#E2E8F0] text-[#64748B] rounded-lg hover:bg-[#E2E8F0] font-medium">
                Semua Pesanan
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] overflow-hidden animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F1F5F9] text-left text-xs text-[#64748B] uppercase">
                        <th class="px-4 py-3">No. Pesanan</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Metode Bayar</th><th class="px-4 py-3">Subtotal</th>
                        <th class="px-4 py-3">Ongkir</th>
                        <th class="px-4 py-3">Total</th><th class="px-4 py-3">Pembayaran</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($orders as $order)
                        <tr class="hover:bg-[#f8fafc]">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm text-orange-600">#{{ $order->no }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <p class="font-medium text-[#1E293B]">{{ $order->customer_name }}</p>
                                <p class="text-xs text-[#94A3B8]">{{ $order->customer_whatsapp }}</p>
                            </td><td class="px-4 py-3 text-xs font-medium uppercase {{ $order->payment_method === "qris" ? "text-amber-700" : "text-[#94A3B8]" }}">{{ $order->payment_method ?? "qris" }}</td>
                            <td class="px-4 py-3 text-sm text-[#94A3B8]">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-[#1E293B]">{{ format_price($order->subtotal) }}</td>
                            <td class="px-4 py-3 text-sm text-[#1E293B]">{{ format_price($order->shipping_cost) }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-[#1E293B]">{{ format_price($order->total) }}</td>
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
                                            class="text-orange-600 hover:text-orange-800 p-1 rounded-lg hover:bg-[#F1F5F9]" title="Quick view">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                                        Lihat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 mx-auto bg-[#F1F5F9] rounded-full mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path>
                                    </svg>
                                </div>
                                <p class="text-[#94A3B8] font-medium">Belum ada pesanan.</p>
                                <p class="text-sm text-[#CBD5E1] mt-1">Order akan muncul di sini setelah pelanggan checkout.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection