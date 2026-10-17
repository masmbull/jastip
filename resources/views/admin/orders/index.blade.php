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

<div class="space-y-5">
    <x-page-header title="Daftar Pesanan" subtitle="Kelola semua pesanan jastip">
        <x-btn as="a" :href="route('admin.invoices.index')" variant="ghost">Invoice Manual</x-btn>
        <x-btn as="a" :href="route('admin.orders.create')" icon="plus">Buat Invoice</x-btn>
    </x-page-header>

    <x-card>
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row gap-2.5" role="search">
            <div class="relative flex-1">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#475569] pointer-events-none" />
                <label class="sr-only" for="order-search">Cari pesanan</label>
                <input id="order-search" type="search" name="search" value="{{ request('search') }}"
                       placeholder="Cari no. pesanan, nama, atau WhatsApp"
                       class="nb-input w-full pl-9 pr-3 py-2 text-sm">
            </div>
            <label class="sr-only" for="order-status">Filter status</label>
            <select id="order-status" name="status" class="nb-input px-3 py-2 text-sm font-bold">
                <option value="all">Semua status</option>
                @foreach(\App\Models\Order::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected($activeStatus === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="nb-btn px-4 py-2 text-sm bg-[#FFC800] text-[#141414]">
                Terapkan
            </button>
            @if(request()->filled('search') || ($activeStatus !== 'all'))
                <a href="{{ route('admin.orders.index') }}"
                   class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E] text-[#475569]">
                    Reset
                </a>
            @endif
        </form>
    </x-card>

    <div class="nb-table-shell">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-3 font-black">No. Pesanan</th>
                        <th scope="col" class="px-4 py-3 font-black">Nama</th>
                        <th scope="col" class="px-4 py-3 font-black">Tanggal</th>
                        <th scope="col" class="px-4 py-3 font-black">Metode Bayar</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Subtotal</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Ongkir</th>
                        <th scope="col" class="px-4 py-3 text-right font-black">Total</th>
                        <th scope="col" class="px-4 py-3 font-black">Pembayaran</th>
                        <th scope="col" class="px-4 py-3 font-black">Status</th>
                        <th scope="col" class="px-4 py-3 font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @forelse($orders as $order)
                        <tr class="bg-white dark:bg-[#1E1E1E] hover:bg-[#FFFDF0] dark:hover:bg-[#262626] transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm font-black text-[#141414] dark:text-[#FAF6EF] dark:text-[#22D3EE]">#{{ $order->no }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <p class="font-extrabold">{{ $order->customer_name }}</p>
                                <p class="text-xs font-medium text-[#475569] num">{{ $order->customer_whatsapp }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-[#475569] num">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-xs font-black uppercase {{ $order->payment_method === 'qris' ? 'text-[#92400E]' : 'text-[#475569]' }}">{{ $order->payment_method ?? 'qris' }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold num">{{ format_price($order->subtotal) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold num">{{ format_price($order->shipping_cost) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-black num">{{ format_price($order->total) }}</td>
                            <td class="px-4 py-3">
                                @if($order->is_paid)
                                    <x-badge tone="success">Lunas</x-badge>
                                @else
                                    <x-badge tone="warning">Belum bayar</x-badge>
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
                                            class="p-2 min-w-[44px] min-h-[44px] inline-flex items-center justify-center text-[#141414] dark:text-[#FAF6EF] border-2 border-transparent hover:border-[#141414] rounded-[8px]"
                                            title="Lihat cepat" aria-label="Lihat cepat {{ $order->no }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="inline-flex items-center min-h-[44px] font-bold text-[#141414] dark:text-[#FAF6EF] dark:text-[#22D3EE] underline underline-offset-2 text-sm">
                                        Lihat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="bg-white dark:bg-[#1E1E1E]">
                                <x-empty-state icon="cart" title="Belum ada pesanan.">
                                    Order akan muncul di sini setelah pelanggan checkout.
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
