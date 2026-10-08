@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('profile.orders') }}" class="text-[#06B6D4] hover:underline mb-6 inline-block"><x-icon name="arrow-left" class="inline-block w-5 h-5 align-text-bottom" /> Kembali ke Pesanan</a>

        <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-8 border border-gray-200 dark:border-[#404854]">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-6">Detail Pesanan #{{ $order->order_number }}</h1>

            {{-- Order Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="p-4 bg-gray-50 dark:bg-[#404854] rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Status</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9] capitalize">{{ $order->status_label }}</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-[#404854] rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Total</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">{{ format_price($order->total) }}</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-[#404854] rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Tanggal</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">{{ $order->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-[#404854] rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Item</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">{{ $order->items->count() }}</p>
                </div>
            </div>

            {{-- Items --}}
            <div class="mb-8 pb-8 border-b border-gray-200 dark:border-[#404854]">
                <h2 class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9] mb-4">Produk Dipesan</h2>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-[#404854] rounded-lg">
                        <div class="flex items-center gap-4">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-[#f1f5f9]">{{ $item->product_name }}</p>
                                <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">x{{ $item->quantity }} · {{ $item->unit ?? 'pcs' }}</p>
                            </div>
                        </div>
                        <p class="font-bold text-gray-900 dark:text-[#f1f5f9]">{{ format_price($item->subtotal) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Shipping --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9] mb-4">Alamat Pengiriman</h2>
                    <p class="text-gray-700 dark:text-[#cbd5e1] whitespace-pre-wrap">
                        {{ $order->customer_address }}
                    </p>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9] mb-4">Informasi Pembayaran</h2>
                    <p class="text-gray-700 dark:text-[#cbd5e1]"><strong>Metode:</strong> {{ strtoupper($order->payment_method ?? 'QRIS') }}</p>
                    @if(($order->discount ?? 0) > 0)
                        <p class="text-gray-700 dark:text-[#cbd5e1] mt-2"><strong>Diskon{{ $order->coupon ? ' (' . $order->coupon->code . ')' : '' }}:</strong> <span class="text-green-600">-{{ format_price($order->discount) }}</span></p>
                    @endif
                    <p class="text-gray-700 dark:text-[#cbd5e1] mt-2"><strong>Total Bayar:</strong> {{ format_price($order->total) }}</p>
                </div>
            </div>

            {{-- Tracking --}}
            @if($order->resi)
                <div class="mt-8 pt-8 border-t border-gray-200 dark:border-[#404854]">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9] mb-4 flex items-center gap-2">
                        <x-icon name="truck" class="w-5 h-5 text-[#0891B2]" /> Pengiriman
                    </h2>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="text-gray-600 dark:text-[#cbd5e1]">Resi:</span>
                        <span class="font-mono font-semibold text-[#0891B2]">{{ $order->resi }}</span>
                        @if($order->courier)
                            <span class="text-gray-600 dark:text-[#cbd5e1]">· {{ $order->courier }}</span>
                        @endif
                        @if($order->tracking_url)
                            <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 font-medium text-[#0891B2] hover:underline">
                                <x-icon name="map-pin" class="w-4 h-4" /> Lacak paket
                            </a>
                        @endif
                    </div>
                    @if($order->shipped_at)
                        <p class="text-xs text-gray-500 dark:text-[#9ca3af] mt-2">Dikirim {{ $order->shipped_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
