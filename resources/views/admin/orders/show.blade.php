@extends('layouts.admin')
@section('title', '#' . $order->no . ' | ' . setting('brand_name'))

@section('content')
@php
    $cardDelay = 120;
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
@endphp

<div class="space-y-6">
    <x-page-header title="Pesanan #{{ $order->no }}" class="animate-fade-up">
        <x-btn as="a" :href="route('admin.orders.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
        <button type="button"
                data-inv-url="{{ route('admin.orders.invoice', $order) }}?embed=1"
                data-inv-title="Invoice #{{ $order->no }}"
                @click="$dispatch('jd-invoice', {url:$el.dataset.invUrl, title:$el.dataset.invTitle})"
                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300 bg-[#0891B2] hover:bg-[#0E7490] text-white shadow-sm">
            <x-icon name="clipboard" class="w-4 h-4" /> Buat Invoice
        </button>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- QRIS Info Card --}}
        <div class="lg:col-span-3 rounded-xl border border-amber-200 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/10 p-5 animate-fade-up" style="animation-delay: {{ $cardDelay }}ms">
            <h2 class="text-sm font-semibold text-amber-800 dark:text-amber-300 uppercase tracking-wide mb-3 flex items-center gap-2">
                <x-icon name="qr-code" class="w-4 h-4" />
                Info Pembayaran QRIS
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-amber-700 dark:text-amber-400 font-medium">Metode:</span>
                    <span class="ml-2 text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->payment_method ?? 'QRIS' }}</span>
                </div>
                <div>
                    <span class="text-amber-700 dark:text-amber-400 font-medium">Status:</span>
                    <span class="ml-2 {{ $order->is_paid ? 'text-green-700 dark:text-green-400' : 'text-amber-700 dark:text-amber-400' }}">{{ $order->is_paid ? 'LUNAS' : 'BELUM BAYAR' }}</span>
                </div>
                <div>
                    <span class="text-amber-700 dark:text-amber-400 font-medium">Waktu Bayar:</span>
                    <span class="ml-2 text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-amber-200 dark:border-amber-500/30">
                <p class="text-xs text-amber-700 dark:text-amber-400 mb-2">QRIS yang digunakan saat checkout:</p>
                @if($qris_image && $qris_image !== '/storage/' && $qris_image !== Storage::url(null))
                    <div class="flex items-center gap-3">
                        <img src="{{ $qris_image }}" alt="QRIS {{ $qris_merchant }}" class="h-20 w-auto rounded border border-amber-200 dark:border-amber-500/30">
                        <span class="text-xs text-amber-700 dark:text-amber-400">{{ $qris_merchant }}</span>
                    </div>
                @else
                    <p class="text-xs text-amber-600 dark:text-amber-400 italic">QRIS belum diunggah. Silakan unggah di halaman pengaturan.</p>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-5 animate-fade-up transition-colors" style="animation-delay: {{ $cardDelay }}ms">
            <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-3">Informasi Pelanggan</h2>
            <div class="space-y-2 text-sm">
                <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Nama:</span> <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->customer_name }}</span></div>
                <div><span class="text-[#64748B] dark:text-[#cbd5e1]">WA:</span> <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_whatsapp) }}" target="_blank" rel="noopener noreferrer" class="text-orange-600 dark:text-orange-400 hover:underline">{{ $order->customer_whatsapp }}</a></div>
                <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Tgl:</span> <span class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->created_at->format('d M Y, H:i') }}</span></div>
                <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Metode:</span> <span class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->shipping_method ?? '-' }}</span></div>
                @if($order->customer_notes)<div><span class="text-[#64748B] dark:text-[#cbd5e1]">Catatan:</span> <span class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->customer_notes }}</span></div>@endif
            </div>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-5 animate-fade-up transition-colors" style="animation-delay: {{ $cardDelay + 60 }}ms">
            <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-3">Ringkasan</h2>
            <div class="space-y-2 text-sm">
                <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Subtotal:</span> <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->subtotal) }}</span></div>
                <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Ongkir:</span> <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->shipping_cost) }}</span></div>
                @if(($order->fee ?? 0) > 0)
                    <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Biaya Fee:</span> <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->fee) }}</span></div>
                @endif
                @if(($order->discount ?? 0) > 0)
                    <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Diskon{{ $order->coupon ? ' (' . $order->coupon->code . ')' : '' }}:</span> <span class="font-medium text-green-600 dark:text-green-400 num">-{{ format_price($order->discount) }}</span></div>
                @endif
                <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-2 mt-2 flex justify-between font-bold">
                    <span class="text-[#1E293B] dark:text-[#f1f5f9]">Total</span><span class="text-orange-600 dark:text-orange-400 num">{{ format_price($order->total) }}</span>
                </div>
                <div class="mt-2 flex items-center gap-2"><span class="text-[#64748B] dark:text-[#cbd5e1]">Status:</span>
                    <x-badge :tone="$statusTones[$order->status] ?? 'neutral'">{{ $order->status_label }}</x-badge>
                </div>
                <div class="mt-2 flex items-center gap-2"><span class="text-[#64748B] dark:text-[#cbd5e1]">Pembayaran:</span>
                    <span class="font-medium uppercase text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->payment_method ?? 'qris' }}</span>
                    @if($order->is_paid)
                        <x-badge tone="success">LUNAS</x-badge>
                    @else
                        <x-badge tone="warning">BELUM BAYAR</x-badge>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-sm border border-[#E2E8F0] dark:border-[#404854] p-5 animate-fade-up transition-colors" style="animation-delay: {{ $cardDelay + 120 }}ms">
            <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-3">Alamat</h2>
            <div class="text-sm">
                <p><span class="text-[#64748B] dark:text-[#cbd5e1]">Pengiriman:</span></p>
                <p class="mt-1 text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->customer_address }}</p>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <x-card padded="false" class="animate-fade-up" style="animation-delay: {{ $cardDelay + 180 }}ms">
        <div class="px-5 py-3 border-b border-[#E2E8F0] dark:border-[#404854]">
            <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide">Item Pesanan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="bg-[#F1F5F9] dark:bg-[#2e323b] text-left text-xs text-[#64748B] dark:text-[#cbd5e1] uppercase">
                        <th class="px-4 py-2">Produk</th>
                        <th class="px-4 py-2 text-center">Jumlah</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#404854]">
                    @foreach($order->items as $item)
                        <tr class="hover:bg-[#f8fafc] dark:hover:bg-[#2e323b] transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://placehold.co/40x40/ffe8f0/999999?text={{ urlencode($item->product_name) }}"
                                         alt="{{ $item->product_name }}" class="w-10 h-10 rounded object-cover" loading="lazy" decoding="async">
                                    <div>
                                        <p class="font-medium text-sm text-[#1E293B] dark:text-[#f1f5f9]">{{ $item->product_name }}</p>
                                        <p class="text-xs text-[#94A3B8] dark:text-[#64748B]">{{ $item->unit ?? 'pcs' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-[#1E293B] dark:text-[#f1f5f9] num">x{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-sm font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-3 animate-fade-up" style="animation-delay: {{ $cardDelay + 240 }}ms">
        @if($order->status === 'pending' || $order->status === 'awaiting_payment')
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="processing">
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Konfirmasi Pesanan
                </button>
            </form>
        @endif
        @if($order->status === 'awaiting_payment' && ! $order->is_paid)
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="confirmed">
                <input type="hidden" name="mark_paid" value="1">
                <button type="submit"
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-green-300">
                    Tandai Lunas &amp; Konfirmasi
                </button>
            </form>
        @endif
        @if(in_array($order->status, ['processing', 'ready']))
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ $order->status === 'processing' ? 'ready' : 'completed' }}">
                <button type="submit"
                        class="px-4 py-2 {{ $order->status === 'processing' ? 'bg-green-500 hover:bg-green-600' : 'bg-[#0891B2] hover:bg-[#0E7490]' }} text-white text-sm font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @if($order->status === 'processing') Tandai Siap Kirim @else Selesaikan Pesanan @endif
                </button>
            </form>
        @endif
        @if(in_array($order->status, ['pending', 'confirmed', 'processing', 'ready']))
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}"
                  class="flex flex-wrap items-center gap-2 bg-[#F8FAFC] dark:bg-[#2e323b] px-3 py-2 rounded-lg border border-[#E2E8F0] dark:border-[#404854]">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ \App\Models\Order::STATUS_SHIPPED }}">
                <input type="text" name="resi" value="{{ $order->resi }}" placeholder="No. resi"
                       class="px-3 py-1.5 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-md w-40 focus:outline-none focus:ring-2 focus:ring-orange-300">
                <input type="text" name="courier" value="{{ $order->courier }}" placeholder="Kurir (mis. JNE)"
                       class="px-3 py-1.5 text-sm border border-[#E2E8F0] dark:border-[#404854] bg-white dark:bg-[#23252b] rounded-md w-36 focus:outline-none focus:ring-2 focus:ring-orange-300">
                <button type="submit"
                        class="inline-flex items-center gap-1 px-4 py-1.5 bg-[#0891B2] hover:bg-[#0E7490] text-white text-sm font-medium rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <x-icon name="truck" class="w-4 h-4" /> Kirim
                </button>
            </form>
        @endif
        <form method="POST"
              action="{{ route('admin.orders.status', $order->id) }}"
              data-confirm="Batalkan pesanan ini?"
              class="inline">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit"
                    class="px-4 py-2 bg-red-400 hover:bg-red-500 text-white text-sm font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-red-300">
                Batalkan
            </button>
        </form>
    </div>
</div>
@endsection