@extends('layouts.admin')
@section('title', '#' . $order->no . ' | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Pesanan #{{ $order->no }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-rose-600 hover:underline ml-2">Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-5">
            <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide mb-3">Informasi Pelanggan</h2>
            <div class="space-y-2 text-sm">
                <div><span class="text-[#999999]">Nama:</span> <span class="font-medium">{{ $order->customer_name }}</span></div>
                <div><span class="text-[#999999]">WA:</span> <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_whatsapp) }}" target="_blank" class="text-rose-600 hover:underline">{{ $order->customer_whatsapp }}</a></div>
                <div><span class="text-[#999999]">Tgl:</span> {{ $order->created_at->format('d M Y, H:i') }}</div>
                <div><span class="text-[#999999]">Metode:</span> {{ $order->shipping_method ?? '-' }}</div>
                @if($order->customer_notes)<div><span class="text-[#999999]">Catatan:</span> {{ $order->customer_notes }}</div>@endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-5">
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

        <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] p-5">
            <h2 class="text-sm font-semibold text-[#666666] uppercase tracking-wide mb-3">Alamat</h2>
            <div class="text-sm">
                <p><span class="text-[#999999]">Pengiriman:</span></p>
                <p class="mt-1">{{ $order->customer_address }}</p>
            </div>
        </div>
    </div>
{{-- Items Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#E8E0D8] overflow-hidden">
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
                                    <img src="https://placehold.co/40x40/ffe8f0/999999?text={{ urlencode($item->product_name) }}" class="w-10 h-10 rounded object-cover">
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

    {{-- Actions --}}
    <div class="flex flex-wrap gap-3">
        @if($order->status === 'pending')
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="processing">
                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow-sm">
                    Konfirmasi Pesanan
                </button>
            </form>
        @endif
        @if(in_array($order->status, ['processing', 'ready']))
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ $order->status === 'processing' ? 'ready' : 'completed' }}">
                <button type="submit" class="px-4 py-2 @if($order->status === 'processing') bg-green-500 hover:bg-green-600 @else bg-purple-500 hover:bg-purple-600 @endif text-white text-sm font-medium rounded-lg shadow-sm">
                    @if($order->status === 'processing') Tandai Siap Kirim @else Selesaikan Pesanan @endif
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('admin.orders.status', $order->id) }}" onsubmit="return confirm('Batalkan pesanan ini?')" class="inline">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="px-4 py-2 bg-red-400 hover:bg-red-500 text-white text-sm font-medium rounded-lg shadow-sm">
                Batalkan
            </button>
        </form>
    </div>
</div>
@endsection