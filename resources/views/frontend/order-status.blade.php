@extends('layouts.app')
@section('title', 'Status Pesanan - ' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-2 num">{{ $order->order_number }}</h1>
        <p class="text-[#64748B] dark:text-[#cbd5e1]">Cek status pesanan kamu di sini</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Status -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Badge -->
            <x-card>
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h2 class="text-xl font-bold text-[#1E293B] dark:text-[#f1f5f9]">Status Pesanan</h2>
                    @php($tone = match ($order->status) {
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'awaiting_payment', 'pending' => 'warning',
                        'shipped' => 'info',
                        default => 'brand',
                    })
                    <x-badge :tone="$tone" class="shrink-0">{{ $order->status_label }}</x-badge>
                </div>

                <!-- Status Timeline -->
                <div class="space-y-4">
                    <!-- Awaiting Payment -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ $order->status !== 'awaiting_payment' ? 'bg-emerald-500' : 'bg-[#0891B2]' }} flex items-center justify-center text-white font-bold">
                                @if($order->status === 'awaiting_payment')
                                    <x-icon name="clock" class="w-5 h-5" />
                                @else
                                    <x-icon name="check" class="w-5 h-5" />
                                @endif
                            </div>
                            @if(!in_array($order->status, ['cancelled']))
                                <div class="w-1 h-8 bg-[#E2E8F0] dark:bg-[#404854] mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1 min-w-0">
                            <p class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Menunggu Pembayaran</p>
                            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] num">{{ $order->created_at->format('d M Y H:i') }}</p>
                            @if($order->status === 'awaiting_payment')
                                <p class="text-xs text-[#0E7490] dark:text-[#67e8f9] mt-1 flex items-center gap-1"><x-icon name="clock" class="w-4 h-4" /> Waktu pembayaran berakhir {{ $order->created_at->addHours(24)->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Confirmed -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['confirmed', 'processing', 'ready', 'shipped', 'completed']) ? 'bg-emerald-500' : 'bg-[#E2E8F0] dark:bg-[#404854]' }} flex items-center justify-center {{ in_array($order->status, ['confirmed', 'processing', 'ready', 'shipped', 'completed']) ? 'text-white' : 'text-[#64748B] dark:text-[#cbd5e1]' }} font-bold">
                                @if(in_array($order->status, ['confirmed', 'processing', 'ready', 'shipped', 'completed']))
                                    <x-icon name="check" class="w-5 h-5" />
                                @else
                                    2
                                @endif
                            </div>
                            @if(!in_array($order->status, ['cancelled']))
                                <div class="w-1 h-8 bg-[#E2E8F0] dark:bg-[#404854] mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1 min-w-0">
                            <p class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Pembayaran Dikonfirmasi</p>
                            @if($order->paid_at)
                                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] num">{{ $order->paid_at->format('d M Y H:i') }}</p>
                            @else
                                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Menunggu verifikasi admin...</p>
                            @endif
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['processing', 'ready', 'shipped', 'completed']) ? 'bg-emerald-500' : 'bg-[#E2E8F0] dark:bg-[#404854]' }} flex items-center justify-center {{ in_array($order->status, ['processing', 'ready', 'shipped', 'completed']) ? 'text-white' : 'text-[#64748B] dark:text-[#cbd5e1]' }} font-bold">
                                @if(in_array($order->status, ['processing', 'ready', 'shipped', 'completed']))
                                    <x-icon name="check" class="w-5 h-5" />
                                @else
                                    3
                                @endif
                            </div>
                            @if(!in_array($order->status, ['cancelled']))
                                <div class="w-1 h-8 bg-[#E2E8F0] dark:bg-[#404854] mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1 min-w-0">
                            <p class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Sedang Diproses</p>
                            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Biasanya 1-3 hari kerja</p>
                        </div>
                    </div>

                    <!-- Shipped -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ in_array($order->status, ['shipped', 'completed']) ? 'bg-emerald-500' : 'bg-[#E2E8F0] dark:bg-[#404854]' }} flex items-center justify-center {{ in_array($order->status, ['shipped', 'completed']) ? 'text-white' : 'text-[#64748B] dark:text-[#cbd5e1]' }} font-bold">
                                @if(in_array($order->status, ['shipped', 'completed']))
                                    <x-icon name="check" class="w-5 h-5" />
                                @else
                                    4
                                @endif
                            </div>
                            @if($order->status !== 'shipped')
                                <div class="w-1 h-8 bg-[#E2E8F0] dark:bg-[#404854] mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1 min-w-0">
                            <p class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Dikirim</p>
                            @if($order->resi)
                                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">
                                    Resi <span class="font-mono font-semibold text-[#0891B2] num">{{ $order->resi }}</span>
                                    @if($order->courier) &middot; {{ $order->courier }} @endif
                                </p>
                                @if($order->tracking_url)
                                    <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1 text-sm font-medium text-[#0891B2] hover:underline mt-1 focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                                        <x-icon name="truck" class="w-4 h-4" />
                                        Lacak paket
                                    </a>
                                @endif
                            @else
                                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Nomor resi akan muncul setelah admin mengirim.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full {{ $order->status === 'completed' ? 'bg-emerald-500' : 'bg-[#E2E8F0] dark:bg-[#404854]' }} flex items-center justify-center {{ $order->status === 'completed' ? 'text-white' : 'text-[#64748B] dark:text-[#cbd5e1]' }} font-bold">
                                @if($order->status === 'completed')
                                    <x-icon name="check" class="w-5 h-5" />
                                @else
                                    5
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 pt-1 min-w-0">
                            <p class="font-semibold text-[#1E293B] dark:text-[#f1f5f9]">Selesai</p>
                            <p class="text-sm text-[#64748B] dark:text-[#cbd5e1]">Paket telah diterima</p>
                        </div>
                    </div>
                </div>

                <!-- Cancellation Alert -->
                @if($order->status === 'cancelled')
                    <div class="mt-6 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <x-icon name="warning" class="w-5 h-5 text-red-600 dark:text-red-300 shrink-0 mt-0.5" />
                            <div>
                                <p class="font-medium text-red-900 dark:text-red-200">Pesanan Dibatalkan</p>
                                <p class="text-sm text-red-700 dark:text-red-300 mt-1">Pesanan ini telah dibatalkan karena melewati batas waktu pembayaran.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </x-card>

            <!-- Items -->
            <x-card>
                <h3 class="text-lg font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Daftar Produk</h3>

                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between gap-4 pb-3 border-b border-[#E2E8F0] dark:border-[#404854] last:border-0">
                            <div class="min-w-0">
                                <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $item->product_name }}</p>
                                <p class="text-sm text-[#64748B] dark:text-[#cbd5e1] num">{{ $item->quantity }} x {{ $item->unit }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-bold text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($item->subtotal) }}</p>
                                <p class="text-xs text-[#64748B] dark:text-[#cbd5e1]">@{{ format_price($item->product_price) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <!-- Summary Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <x-card>
                <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Ringkasan</h3>

                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between gap-4">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">Subtotal</span>
                        <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-[#64748B] dark:text-[#cbd5e1]">Ongkir</span>
                        <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->shipping_cost) }}</span>
                    </div>
                    @if($order->fee > 0)
                        <div class="flex justify-between gap-4">
                            <span class="text-[#64748B] dark:text-[#cbd5e1]">Biaya Fee</span>
                            <span class="font-medium text-[#1E293B] dark:text-[#f1f5f9] num">{{ format_price($order->fee) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-[#E2E8F0] dark:border-[#404854] pt-3 flex justify-between gap-4">
                        <span class="font-bold text-[#1E293B] dark:text-[#f1f5f9]">Total</span>
                        <span class="text-lg font-bold text-[#0E7490] dark:text-[#67e8f9] num">{{ format_price($order->total) }}</span>
                    </div>
                </div>

                <div class="bg-[#F1F5F9] dark:bg-[#2e323b] rounded-lg p-3 text-xs text-[#64748B] dark:text-[#cbd5e1]">
                    Pesanan dibuat {{ $order->created_at->diffForHumans() }}
                </div>
            </x-card>

            <!-- Customer Info -->
            <x-card>
                <h3 class="font-bold text-[#1E293B] dark:text-[#f1f5f9] mb-4">Informasi Pelanggan</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-[#64748B] dark:text-[#cbd5e1]">Nama</p>
                        <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9]">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-[#64748B] dark:text-[#cbd5e1]">WhatsApp</p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_whatsapp) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="font-medium text-[#0E7490] dark:text-[#67e8f9] hover:underline num focus:outline-none focus:ring-2 focus:ring-orange-300 rounded">
                            {{ $order->customer_whatsapp }}
                        </a>
                    </div>
                    <div>
                        <p class="text-[#64748B] dark:text-[#cbd5e1]">Alamat</p>
                        <p class="font-medium text-[#1E293B] dark:text-[#f1f5f9] text-xs">{{ $order->customer_address }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Back Button -->
            <a href="{{ route('home') }}"
               class="w-full flex items-center justify-center gap-2 min-h-[44px] px-4 py-2.5 border border-[#E2E8F0] dark:border-[#404854] text-[#64748B] dark:text-[#cbd5e1] hover:bg-[#F8FAFC] dark:hover:bg-[#2e323b] rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-orange-300">
                <x-icon name="arrow-left" class="w-5 h-5" />
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