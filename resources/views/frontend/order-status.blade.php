@extends('layouts.app')
@section('title', 'Status Pesanan - ' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-[#1E293B] mb-2">{{ $order->order_number }}</h1>
        <p class="text-[#64748B]">Cek status pesanan kamu di sini</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Status -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Badge -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-[#1E293B]">Status Pesanan</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $order->status_badge_class }}">
                        {{ $order->status_label }}
                    </span>
                </div>

                <!-- Status Timeline -->
                <div class="space-y-4">
                    <!-- Awaiting Payment -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ $order->status !== 'awaiting_payment' ? 'bg-emerald-500' : 'bg-amber-500' }} flex items-center justify-center text-white font-bold">
                                @if($order->status === 'awaiting_payment')
                                    ⏳
                                @else
                                    ✓
                                @endif
                            </div>
                            @if(!in_array($order->status, ['cancelled']))
                                <div class="w-1 h-8 bg-[#E2E8F0] mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-[#1E293B]">Menunggu Pembayaran</p>
                            <p class="text-sm text-[#64748B]">{{ $order->created_at->format('d M Y H:i') }}</p>
                            @if($order->status === 'awaiting_payment')
                                <p class="text-xs text-amber-600 mt-1">⏱️ Waktu pembayaran berakhir {{ $order->created_at->addHours(24)->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Confirmed -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['confirmed', 'processing', 'ready', 'shipped', 'completed']) ? 'bg-emerald-500' : 'bg-[#E2E8F0]' }} flex items-center justify-center {{ in_array($order->status, ['confirmed', 'processing', 'ready', 'shipped', 'completed']) ? 'text-white' : 'text-[#94A3B8]' }} font-bold">
                                @if(in_array($order->status, ['confirmed', 'processing', 'ready', 'shipped', 'completed']))
                                    ✓
                                @else
                                    2
                                @endif
                            </div>
                            @if(!in_array($order->status, ['confirmed', 'cancelled']))
                                <div class="w-1 h-8 bg-[#E2E8F0] mt-1"></div>
                            @elseif(!in_array($order->status, ['cancelled']))
                                <div class="w-1 h-8 bg-[#E2E8F0] mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-[#1E293B]">Pembayaran Dikonfirmasi</p>
                            @if($order->paid_at)
                                <p class="text-sm text-[#64748B]">{{ $order->paid_at->format('d M Y H:i') }}</p>
                            @else
                                <p class="text-sm text-[#94A3B8]">Menunggu verifikasi admin...</p>
                            @endif
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['processing', 'ready', 'shipped', 'completed']) ? 'bg-emerald-500' : 'bg-[#E2E8F0]' }} flex items-center justify-center {{ in_array($order->status, ['processing', 'ready', 'shipped', 'completed']) ? 'text-white' : 'text-[#94A3B8]' }} font-bold">
                                @if(in_array($order->status, ['processing', 'ready', 'shipped', 'completed']))
                                    ✓
                                @else
                                    3
                                @endif
                            </div>
                            @if(!in_array($order->status, ['processing', 'cancelled']))
                                <div class="w-1 h-8 bg-[#E2E8F0] mt-1"></div>
                            @elseif(!in_array($order->status, ['cancelled']))
                                <div class="w-1 h-8 bg-[#E2E8F0] mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-[#1E293B]">Sedang Diproses</p>
                            <p class="text-sm text-[#94A3B8]">Biasanya 1-3 hari kerja</p>
                        </div>
                    </div>

                    <!-- Shipped -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['shipped', 'completed']) ? 'bg-emerald-500' : 'bg-[#E2E8F0]' }} flex items-center justify-center {{ in_array($order->status, ['shipped', 'completed']) ? 'text-white' : 'text-[#94A3B8]' }} font-bold">
                                @if(in_array($order->status, ['shipped', 'completed']))
                                    ✓
                                @else
                                    4
                                @endif
                            </div>
                            @if($order->status !== 'shipped')
                                <div class="w-1 h-8 bg-[#E2E8F0] mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-[#1E293B]">Dikirim</p>
                            <p class="text-sm text-[#94A3B8]">Tracking akan diberikan saat pengiriman</p>
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ $order->status === 'completed' ? 'bg-emerald-500' : 'bg-[#E2E8F0]' }} flex items-center justify-center {{ $order->status === 'completed' ? 'text-white' : 'text-[#94A3B8]' }} font-bold">
                                @if($order->status === 'completed')
                                    ✓
                                @else
                                    5
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="font-semibold text-[#1E293B]">Selesai</p>
                            <p class="text-sm text-[#94A3B8]">Paket telah diterima</p>
                        </div>
                    </div>
                </div>

                <!-- Cancellation Alert -->
                @if($order->status === 'cancelled')
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-medium text-red-900">Pesanan Dibatalkan</p>
                                <p class="text-sm text-red-700 mt-1">Pesanan ini telah dibatalkan karena melewati batas waktu pembayaran.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-[#1E293B] mb-4">Daftar Produk</h3>

                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0] last:border-0">
                            <div>
                                <p class="font-medium text-[#1E293B]">{{ $item->product_name }}</p>
                                <p class="text-sm text-[#64748B]">{{ $item->quantity }} x {{ $item->unit }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-[#1E293B]">{{ format_price($item->subtotal) }}</p>
                                <p class="text-xs text-[#64748B]">@{{ format_price($item->product_price) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-[#1E293B] mb-4">Ringkasan</h3>

                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Subtotal</span>
                        <span class="font-medium text-[#1E293B]">{{ format_price($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Ongkir</span>
                        <span class="font-medium text-[#1E293B]">{{ format_price($order->shipping_cost) }}</span>
                    </div>
                    @if($order->fee > 0)
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Biaya Fee</span>
                            <span class="font-medium text-[#1E293B]">{{ format_price($order->fee) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-[#E2E8F0] pt-3 flex justify-between">
                        <span class="font-bold text-[#1E293B]">Total</span>
                        <span class="text-lg font-bold text-orange-600">{{ format_price($order->total) }}</span>
                    </div>
                </div>

                <div class="bg-[#F1F5F9] rounded-lg p-3 text-xs text-[#64748B]">
                    Pesanan dibuat {{ $order->created_at->diffForHumans() }}
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-[#1E293B] mb-4">Informasi Pelanggan</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-[#64748B]">Nama</p>
                        <p class="font-medium text-[#1E293B]">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-[#64748B]">WhatsApp</p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_whatsapp) }}"
                           target="_blank"
                           class="font-medium text-orange-600 hover:text-orange-700">
                            {{ $order->customer_whatsapp }}
                        </a>
                    </div>
                    <div>
                        <p class="text-[#64748B]">Alamat</p>
                        <p class="font-medium text-[#1E293B] text-xs">{{ $order->customer_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <a href="{{ route('home') }}"
               class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-[#E2E8F0] text-[#64748B] hover:bg-[#F8FAFC] rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 110 0m0 0l3-3m-3 3l-3-3"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
    // Poll order status every 10 seconds
    setInterval(function() {
        fetch('{{ route("order.status", $order->order_number) }}')
            .then(response => response.json())
            .then(data => {
                // Refresh page if status changed
                if (data.status !== '{{ $order->status }}') {
                    window.location.reload();
                }
            })
            .catch(err => console.log('Status check error:', err));
    }, 10000);
</script>
@endsection
