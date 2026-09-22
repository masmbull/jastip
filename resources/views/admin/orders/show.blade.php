@extends('layouts.admin')
@section('title', '#' . $order->no . ' | ' . setting('brand_name'))

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between animate-fade-up">
        <div>
            <h1 class="text-2xl font-bold text-[#1E293B]">Pesanan #{{ $order->no }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-orange-600 hover:underline ml-2">Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @php($cardDelay = 120)

        {{-- QRIS Info Card --}}
        <div class="lg:col-span-3 bg-amber-50 rounded-xl border border-amber-200 p-5 mb-4 animate-fade-up" style="animation-delay: {{ $cardDelay }}ms">
            <h2 class="text-sm font-semibold text-amber-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="3" height="3"></rect><rect x="18" y="18" width="3" height="3"></rect></svg>
                Info Pembayaran QRIS
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-amber-700 font-medium">Metode:</span>
                    <span class="ml-2 text-[#1E293B]">{{ $order->payment_method ?? 'QRIS' }}</span>
                </div>
                <div>
                    <span class="text-amber-700 font-medium">Status:</span>
                    <span class="ml-2 {{ $order->is_paid ? 'text-green-700' : 'text-amber-700' }}">{{ $order->is_paid ? 'LUNAS' : 'BELUM BAYAR' }}</span>
                </div>
                <div>
                    <span class="text-amber-700 font-medium">Waktu Bayar:</span>
                    <span class="ml-2 text-[#1E293B]">{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-amber-200">
                <p class="text-xs text-amber-700 mb-2">QRIS yang digunakan saat checkout:</p>
                @if($qris_image && $qris_image !== '/storage/' && $qris_image !== Storage::url(null))
                    <div class="flex items-center gap-3">
                        <img src="{{ $qris_image }}" alt="QRIS {{ $qris_merchant }}" class="h-20 w-auto rounded border">
                        <span class="text-xs text-amber-700">{{ $qris_merchant }}</span>
                    </div>
                @else
                    <p class="text-xs text-amber-600 italic">QRIS belum diunggah. Silakan unggah di halaman pengaturan.</p>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-5 animate-fade-up" style="animation-delay: {{ $cardDelay }}ms">
            <h2 class="text-sm font-semibold text-[#64748B] uppercase tracking-wide mb-3">Informasi Pelanggan</h2>
            <div class="space-y-2 text-sm">
                <div><span class="text-[#94A3B8]">Nama:</span> <span class="font-medium">{{ $order->customer_name }}</span></div>
                                <div><span class="text-[#94A3B8]">WA:</span> <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_whatsapp) }}" target="_blank" rel="noopener noreferrer" class="text-orange-600 hover:underline">{{ $order->customer_whatsapp }}</a></div>
                <div><span class="text-[#94A3B8]">Tgl:</span> {{ $order->created_at->format('d M Y, H:i') }}</div>
                <div><span class="text-[#94A3B8]">Metode:</span> {{ $order->shipping_method ?? '-' }}</div>
                @if($order->customer_notes)<div><span class="text-[#94A3B8]">Catatan:</span> {{ $order->customer_notes }}</div>@endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-5 animate-fade-up" style="animation-delay: {{ $cardDelay + 60 }}ms">
            <h2 class="text-sm font-semibold text-[#64748B] uppercase tracking-wide mb-3">Ringkasan</h2>
            <div class="space-y-2 text-sm">
                <div><span class="text-[#94A3B8]">Subtotal:</span> <span class="font-medium">{{ format_price($order->subtotal) }}</span></div>
                <div><span class="text-[#94A3B8]">Ongkir:</span> <span class="font-medium">{{ format_price($order->shipping_cost) }}</span></div>
                <div class="border-t border-[#E2E8F0] pt-2 mt-2 flex justify-between font-bold">
                    <span>Total</span><span class="text-orange-600">{{ format_price($order->total) }}</span>
                </div>
                <div class="mt-2"><span class="text-[#94A3B8]">Status:</span>
                    <span class="ml-1 px-2 py-1 text-xs rounded-full {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                </div>
                <div class="mt-2"><span class="text-[#94A3B8]">Pembayaran:</span>
                    <span class="font-medium uppercase">{{ $order->payment_method ?? 'qris' }}</span>
                    @if($order->is_paid)
                        <span class="ml-1 px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">LUNAS</span>
                    @else
                        <span class="ml-1 px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800">BELUM BAYAR</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] p-5 animate-fade-up" style="animation-delay: {{ $cardDelay + 120 }}ms">
            <h2 class="text-sm font-semibold text-[#64748B] uppercase tracking-wide mb-3">Alamat</h2>
            <div class="text-sm">
                <p><span class="text-[#94A3B8]">Pengiriman:</span></p>
                <p class="mt-1">{{ $order->customer_address }}</p>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#E2E8F0] overflow-hidden animate-fade-up" style="animation-delay: {{ $cardDelay + 180 }}ms">
        <div class="px-5 py-3 border-b border-[#E2E8F0]">
            <h2 class="text-sm font-semibold text-[#64748B] uppercase tracking-wide">Item Pesanan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#F1F5F9] text-left text-xs text-[#64748B] uppercase">
                        <th class="px-4 py-2">Produk</th>
                        <th class="px-4 py-2">Jumlah</th>
                        <th class="px-4 py-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @foreach($order->items as $item)
                        <tr class="hover:bg-[#f8fafc]">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://placehold.co/40x40/ffe8f0/999999?text={{ urlencode($item->product_name) }}"
                                         alt="{{ $item->product_name }}" class="w-10 h-10 rounded object-cover" loading="lazy" decoding="async">
                                    <div>
                                        <p class="font-medium text-sm text-[#1E293B]">{{ $item->product_name }}</p>
                                        <p class="text-xs text-[#94A3B8]">{{ $item->unit ?? 'pcs' }}</p>
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
    <div class="flex flex-wrap gap-3 animate-fade-up" style="animation-delay: {{ $cardDelay + 240 }}ms">
        @if($order->status === 'pending' || $order->status === 'awaiting_payment')
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="processing">
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
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
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                    Tandai Lunas & Konfirmasi
                </button>
            </form>
        @endif
        @if(in_array($order->status, ['processing', 'ready']))
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ $order->status === 'processing' ? 'ready' : 'completed' }}">
                <button type="submit"
                        class="px-4 py-2 {{ $order->status === 'processing' ? 'bg-green-500 hover:bg-green-600' : 'bg-purple-500 hover:bg-purple-600' }} text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @if($order->status === 'processing') Tandai Siap Kirim @else Selesaikan Pesanan @endif
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
                    class="px-4 py-2 bg-red-400 hover:bg-red-500 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                Batalkan
            </button>
        </form>
    </div>
</div>
@endsection