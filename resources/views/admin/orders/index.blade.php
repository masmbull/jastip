@extends('layouts.admin')
@section('title', 'Pesanan | ' . setting('brand_name'))

@section('content')
@php
    $statusTones = [
        'awaiting_payment' => 'warning',
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'brand',
        'ready' => 'neutral',
        'shipped' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];
    $activeStatus = request('status', 'all');
@endphp

<div class="space-y-6">
    <x-page-header title="Daftar Pesanan" subtitle="Kelola semua pesanan jastip" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.invoices.index')" variant="ghost">Invoice Manual</x-btn>
        <x-btn as="a" :href="route('admin.orders.create')" icon="plus">Buat Invoice</x-btn>
    </x-page-header>

    <x-card class="animate-fade-up" style="animation-delay: 60ms">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8] pointer-events-none" />
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="Cari no. pesanan, nama, atau WhatsApp"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <select name="status"
                    class="px-3 py-2 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#2e323b] text-[#1E293B] dark:text-[#f1f5f9] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300">
                <option value="all">Semua status</option>
                @foreach(\App\Models\Order::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="px-4 py-2 text-sm bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                Terapkan
            </button>
            @if(request()->filled('search') || ($activeStatus !== 'all'))
                <a href="{{ route('admin.orders.index') }}"
                   class="px-4 py-2 text-sm text-[#64748B] dark:text-[#cbd5e1] hover:text-orange-600 dark:hover:text-orange-400 rounded-lg font-medium text-center transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </x-card>

    <x-card padded="false" class="animate-fade-up" style="animation-delay: 120ms">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-3">No. Pesanan</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Metode Bayar</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                        <th class="px-4 py-3 text-right">Ongkir</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3">Pembayaran</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854] stagger">
                    @forelse($orders as $order)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm text-orange-600 dark:text-orange-400">#{{ $order->no }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->customer_name }}</p>
                                <p class="text-xs text-[#94A3B8] dark:text-[#64748B]">{{ $order->customer_whatsapp }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-[#64748B] dark:text-[#cbd5e1]">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-xs font-medium uppercase {{ $order->payment_method === 'qris' ? 'text-amber-700 dark:text-amber-400' : 'text-[#64748B] dark:text-[#cbd5e1]' }}">{{ $order->payment_method ?? 'qris' }}</td>
                            <td class="px-4 py-3 text-sm text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->subtotal) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->shipping_cost) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->total) }}</td>
                            <td class="px-4 py-3">
                                @if($order->is_paid)
                                    <x-badge tone="success">LUNAS</x-badge>
                                @else
                                    <x-badge tone="warning">BELUM BAYAR</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-badge :tone="$statusTones[$order->status] ?? 'neutral'">{{ $order->status_label }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                            data-qv-url="{{ route('admin.orders.quickview', $order) }}"
                                            data-qv-title="#{{ $order->no }}"
                                            @click="$dispatch('quickview', {url:$el.dataset.qvUrl, title:$el.dataset.qvTitle})"
                                            class="text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 p-1.5 rounded-lg hover:bg-[#F1F5F9] dark:hover:bg-[#2e323b] transition-colors"
                                            title="Lihat cepat" aria-label="Lihat cepat {{ $order->no }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 text-sm font-medium transition-colors">
                                        Lihat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <x-empty-state icon="cart" title="Belum ada pesanan.">
                                    Order akan muncul di sini setelah pelanggan checkout.
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