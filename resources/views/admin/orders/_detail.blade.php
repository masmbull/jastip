@php($cardDelay = 120)

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    {{-- Customer --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-5 animate-fade-up" style="animation-delay: {{ $cardDelay }}ms">
        <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide mb-3">Informasi Pelanggan</h2>
        <div class="space-y-2 text-sm">
            <div><span class="text-[#999999]">Nama:</span> <span class="font-medium">{{ $order->customer_name }}</span></div>
            <div><span class="text-[#999999]">WA:</span>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_whatsapp) }}" target="_blank" rel="noopener noreferrer" class="text-rose-600 hover:underline">{{ $order->customer_whatsapp }}</a>
            </div>
            <div><span class="text-[#999999]">Tgl:</span> {{ $order->created_at->format('d M Y, H:i') }}</div>
            <div><span class="text-[#999999]">Metode:</span> {{ $order->shipping_method ?? '-' }}</div>
            @if($order->customer_notes)
                <div><span class="text-[#999999]">Catatan:</span> {{ $order->customer_notes }}</div>
            @endif
        </div>
    </div>

    {{-- Summary --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-5 animate-fade-up" style="animation-delay: {{ $cardDelay + 60 }}ms">
        <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide mb-3">Ringkasan</h2>
        <div class="space-y-2 text-sm">
            <div><span class="text-[#999999]">Subtotal:</span> <span class="font-medium">{{ format_price($order->subtotal) }}</span></div>
            <div><span class="text-[#999999]">Ongkir:</span> <span class="font-medium">{{ format_price($order->shipping_cost) }}</span></div>
            <div class="border-t border-[#F0EDE7] pt-2 mt-2 flex justify-between font-bold">
                <span>Total</span><span class="text-rose-600">{{ format_price($order->total) }}</span>
            </div>
            <div class="mt-2"><span class="text-[#999999]">Status:</span>
                <span class="ml-1 px-2 py-1 text-xs rounded-full {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
            </div>
        </div>
    </div>

    {{-- Address --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-5 animate-fade-up" style="animation-delay: {{ $cardDelay + 120 }}ms">
        <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide mb-3">Alamat</h2>
        <div class="text-sm">
            <p><span class="text-[#999999]">Pengiriman:</span></p>
            <p class="mt-1">{{ $order->customer_address }}</p>
        </div>
    </div>
</div>

{{-- Items --}}
<div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] overflow-hidden animate-fade-up" style="animation-delay: {{ $cardDelay + 180 }}ms">
    <div class="px-5 py-3 border-b border-[#E8E0D8]">
        <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide">Item Pesanan</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#F8F5F0] text-left text-xs text-[#666666] uppercase">
                    <th class="px-4 py-2">Produk</th>
                    <th class="px-4 py-2">Jumlah</th>
                    <th class="px-4 py-2">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F0EDE7]">
                @foreach($order->items as $item)
                    <tr class="hover:bg-[#fafafa]">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://placehold.co/40x40/ffe8f0/999999?text={{ urlencode($item->product_name) }}"
                                     alt="{{ $item->product_name }}" class="w-10 h-10 rounded object-cover" loading="lazy" decoding="async">
                                <div>
                                    <p class="font-medium text-sm text-[#333333]">{{ $item->product_name }}</p>
                                    <p class="text-xs text-[#999999]">{{ $item->unit ?? 'pcs' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-sm">x{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium">{{ format_price($item->subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
