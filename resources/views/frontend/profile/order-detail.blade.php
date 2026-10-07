@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('profile.orders') }}" class="text-[#fb923c] hover:underline mb-6 inline-block">← Kembali ke Pesanan</a>

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
                    <p class="text-gray-700 dark:text-[#cbd5e1] mt-2"><strong>Total Bayar:</strong> {{ format_price($order->total) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
