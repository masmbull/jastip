@php($cardDelay = 120)

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    {{-- Customer --}}
    <x-card class="animate-fade-up" style="animation-delay: {{ $cardDelay }}ms">
        <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-3">Informasi Pelanggan</h2>
        <div class="space-y-2 text-sm">
            <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Nama:</span> <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->customer_name }}</span></div>
            <div><span class="text-[#64748B] dark:text-[#cbd5e1]">WA:</span>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_whatsapp) }}" target="_blank" rel="noopener noreferrer" class="text-orange-600 dark:text-orange-400 hover:underline">{{ $order->customer_whatsapp }}</a>
            </div>
            <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Tgl:</span> <span class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->created_at->format('d M Y, H:i') }}</span></div>
            <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Metode:</span> <span class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->shipping_method ?? '-' }}</span></div>
            @if($order->customer_notes)
                <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Catatan:</span> <span class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->customer_notes }}</span></div>
            @endif
        </div>
    </x-card>

    {{-- Summary --}}
    <x-card class="animate-fade-up" style="animation-delay: {{ $cardDelay + 60 }}ms">
        <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-3">Ringkasan</h2>
        <div class="space-y-2 text-sm">
            <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Subtotal:</span> <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->subtotal) }}</span></div>
            <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Ongkir:</span> <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->shipping_cost) }}</span></div>
            @if(($order->fee ?? 0) > 0)
                <div><span class="text-[#64748B] dark:text-[#cbd5e1]">Biaya Fee:</span> <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->fee) }}</span></div>
            @endif
            <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-2 mt-2 flex justify-between font-bold">
                <span class="text-[#1E293B] dark:text-[#f1f5f9]">Total</span><span class="text-orange-600 dark:text-orange-400 num">{{ format_price($order->total) }}</span>
            </div>
            <div class="mt-2 flex items-center gap-2"><span class="text-[#64748B] dark:text-[#cbd5e1]">Status:</span>
                <x-badge :tone="$order->is_paid ? 'success' : 'warning'">{{ $order->status_label }}</x-badge>
            </div>
            <div class="mt-2 flex items-center gap-2"><span class="text-[#64748B] dark:text-[#cbd5e1]">Pembayaran:</span>
                <span class="font-medium uppercase text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->payment_method ?? 'qris' }}</span>
                @if($order->is_paid)
                    <x-badge tone="success">LUNAS</x-badge>
                @else
                    <x-badge tone="warning">BELUM BAYAR</x-badge>
                @endif
            </div>
            <div class="mt-2"><span class="text-[#64748B] dark:text-[#cbd5e1]">Bayar pada:</span>
                <span class="ml-1 text-sm text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}</span>
            </div>
            @if($qris_image && $qris_image !== '/storage/')
                <div class="mt-3 pt-2 border-t border-[#E2E8F0] dark:border-[#404854]">
                    <p class="text-xs text-[#64748B] dark:text-[#cbd5e1] mb-1">QRIS: <span class="text-[#1E293B] dark:text-[#f1f5f9]">{{ $qris_merchant ?? 'NITIP DI END' }}</span></p>
                    <img src="{{ $qris_image }}" alt="QRIS {{ $qris_merchant }}" class="h-16 w-auto rounded border border-[#E2E8F0] dark:border-[#404854]">
                </div>
            @endif
        </div>
    </x-card>

    {{-- Address --}}
    <x-card class="animate-fade-up" style="animation-delay: {{ $cardDelay + 120 }}ms">
        <h2 class="text-sm font-semibold text-[#64748B] dark:text-[#cbd5e1] uppercase tracking-wide mb-3">Alamat</h2>
        <div class="text-sm">
            <p><span class="text-[#64748B] dark:text-[#cbd5e1]">Pengiriman:</span></p>
            <p class="mt-1 text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->customer_address }}</p>
        </div>
    </x-card>
</div>

{{-- Items --}}
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