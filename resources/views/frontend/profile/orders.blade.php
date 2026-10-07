@extends('layouts.app')

@section('title', 'Pesanan Saya | NITIP DI END')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#FDF6EC] to-[#ede7de] dark:from-[#1a1a1a] dark:to-[#23252b] py-8">
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
                        <p class="text-lg font-bold text-gray-900 dark:text-[#f1f5f9]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-[#cbd5e1]">Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-medium
                            {{ $order->status === 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 
                               ($order->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' :
                               'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200') }}">
                            {{ $order->status }}
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
                        <p class="text-gray-700 dark:text-[#cbd5e1]">{{ $item->product->name }} x{{ $item->quantity }}</p>
                        <p class="text-gray-700 dark:text-[#cbd5e1]">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-[#404854]">
                    <a href="{{ route('profile.order-detail', $order) }}" class="flex-1 px-4 py-2 text-center text-[#fb923c] hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded transition">
                        Lihat Detail
                    </a>
                    @if($order->status === 'pending')
                    <a href="{{ route('payment.waiting', $order) }}" class="flex-1 px-4 py-2 text-center bg-[#fb923c] text-white rounded hover:bg-[#e6951b] transition">
                        Bayar
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-[#9ca3af] text-lg mb-4">Belum ada pesanan</p>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-[#fb923c] text-white rounded-lg hover:bg-[#e6951b] transition inline-block">
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
