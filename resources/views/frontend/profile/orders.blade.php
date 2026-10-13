@extends('layouts.app')

@section('title', 'Pesanan Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#ecfeff] to-[#e0f2fe] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-8">Pesanan Saya</h1>

        <div class="space-y-4">
            @forelse($orders as $order)
            <x-card>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 pb-4 border-b border-[#E2E8F0] dark:border-[#404854]">
                    <div>
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">No. Pesanan</p>
                        <p class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] num">#{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Total</p>
                        <p class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->total) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Status</p>
                        <div class="mt-1">
                            @php($tone = match ($order->status) {
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'awaiting_payment', 'pending' => 'warning',
                                'shipped' => 'info',
                                default => 'brand',
                            })
                            <x-badge :tone="$tone">{{ $order->status_label }}</x-badge>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Tanggal</p>
                        <p class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] num">{{ $order->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-[#64748B] dark:text-[#cbd5e1]">{{ $item->product_name }} x{{ $item->quantity }}</p>
                        <p class="text-[#1E293B] dark:text-[#f1f5f9] num shrink-0">{{ format_price($item->subtotal) }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-[#E2E8F0] dark:border-[#404854]">
                    <x-btn as="a" :href="route('profile.order-detail', $order)" variant="ghost" class="flex-1">Lihat Detail</x-btn>
                    @if($order->status === \App\Models\Order::STATUS_AWAITING_PAYMENT)
                        <x-btn as="a" :href="route('payment.waiting', $order)" variant="brand" class="flex-1">Bayar</x-btn>
                    @endif
                </div>
            </x-card>
            @empty
            <x-card>
                <x-empty-state icon="receipt-percent" title="Belum ada pesanan">
                    Kamu belum menitip apa pun. Mulai dari katalog produk.
                    <div class="mt-4">
                        <x-btn as="a" :href="route('products.index')" variant="brand" icon="bag">Mulai Berbelanja</x-btn>
                    </div>
                </x-empty-state>
            </x-card>
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