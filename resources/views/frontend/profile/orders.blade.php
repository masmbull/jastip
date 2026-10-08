@extends('layouts.app')

@section('title', 'Pesanan Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-[#f1f5f9] mb-8">Pesanan Saya</h1>

        <div class="space-y-4">
            @forelse($orders as $order)
            <div class="bg-white dark:bg-[#23252b] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-[#404854]">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 pb-4 border-b border-gray-200 dark:border-[#404854]">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">No. Pesanan</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">#{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Total</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">{{ format_price($order->total) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-medium {{ $order->status_badge_class }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Tanggal</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">{{ $order->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between">
                        <p class="text-gray-700 dark:text-[#cbd5e1]">{{ $item->product_name }} x{{ $item->quantity }}</p>
                        <p class="text-gray-700 dark:text-[#cbd5e1]">{{ format_price($item->subtotal) }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-[#404854]">
                    <a href="{{ route('profile.order-detail', $order) }}" class="flex-1 px-4 py-2 text-center text-[#06B6D4] hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded transition">
                        Lihat Detail
                    </a>
                    @if($order->status === \App\Models\Order::STATUS_AWAITING_PAYMENT)
                    <a href="{{ route('payment.waiting', $order) }}" class="flex-1 px-4 py-2 text-center bg-[#06B6D4] text-white rounded hover:bg-[#0E7490] transition">
                        Bayar
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-[#9ca3af] text-lg mb-4">Belum ada pesanan</p>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-[#06B6D4] text-white rounded-lg hover:bg-[#0E7490] transition inline-block">
                    Mulai Berbelanja
                </a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
