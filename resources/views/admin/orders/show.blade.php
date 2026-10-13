@extends('layouts.admin')
@section('title', '#' . $order->no . ' | ' . setting('brand_name'))

@section('content')
@php
    $statusTones = [
        'awaiting_payment' => 'warning',
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'brand',
        'ready' => 'neutral',
        'shipped' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];
@endphp

<div class="space-y-5">
    <x-page-header title="Pesanan #{{ $order->no }}">
        <x-btn as="a" :href="route('admin.orders.index')" variant="ghost" icon="arrow-left">Kembali</x-btn>
        <button type="button"
                data-inv-url="{{ route('admin.orders.invoice', $order) }}?embed=1"
                data-inv-title="Invoice #{{ $order->no }}"
                @click="$dispatch('jd-invoice', {url:$el.dataset.invUrl, title:$el.dataset.invTitle})"
                class="nb-btn px-4 py-2 text-sm bg-[#0E7490] text-white">
            <x-icon name="clipboard" class="w-4 h-4" /> Buat Invoice
        </button>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-3 nb-card p-5 bg-[#FFF7D6]! dark:bg-[#262626]!">
            <h2 class="text-[12px] font-black text-[#92400E] dark:text-[#FDE68A] uppercase tracking-[0.12em] mb-3 flex items-center gap-2">
                <x-icon name="qr-code" class="w-4 h-4" />
                Info Pembayaran QRIS
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                <div class="border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[8px] bg-white dark:bg-[#1E1E1E] px-3 py-2">
                    <span class="block text-[11px] font-black uppercase tracking-wider text-[#475569]">Metode</span>
                    <span class="font-extrabold text-[#141414] dark:text-[#FAF6EF]">{{ $order->payment_method ?? 'QRIS' }}</span>
                </div>
                <div class="border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[8px] bg-white dark:bg-[#1E1E1E] px-3 py-2">
                    <span class="block text-[11px] font-black uppercase tracking-wider text-[#475569]">Status</span>
                    <span class="font-black {{ $order->is_paid ? 'text-[#166534]' : 'text-[#92400E]' }}">{{ $order->is_paid ? 'LUNAS' : 'BELUM BAYAR' }}</span>
                </div>
                <div class="border-2 border-[#141414] dark:border-[#FAF6EF] rounded-[8px] bg-white dark:bg-[#1E1E1E] px-3 py-2">
                    <span class="block text-[11px] font-black uppercase tracking-wider text-[#475569]">Waktu Bayar</span>
                    <span class="font-extrabold text-[#141414] dark:text-[#FAF6EF] num">{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t-2 border-[#141414] dark:border-[#FAF6EF]">
                <p class="text-xs font-bold text-[#475569] dark:text-[#D6D3D1] mb-2">QRIS yang digunakan saat checkout:</p>
                @if($qris_image && $qris_image !== '/storage/' && $qris_image !== Storage::url(null))
                    <div class="flex items-center gap-3">
                        <img src="{{ $qris_image }}" alt="QRIS {{ $qris_merchant }}" class="h-20 w-auto rounded-[8px] border-2 border-[#141414] bg-white">
                        <span class="text-xs font-bold">{{ $qris_merchant }}</span>
                    </div>
                @else
                    <p class="text-xs font-bold italic">QRIS belum diunggah. Unggah di halaman Pengaturan.</p>
                @endif
            </div>
        </div>

        <x-card>
            <h2 class="text-[12px] font-black text-[#475569] dark:text-[#D6D3D1] uppercase tracking-[0.12em] mb-3">Informasi Pelanggan</h2>
            <div class="space-y-2 text-sm">
                <div><span class="text-[#475569] font-bold">Nama:</span> <span class="font-extrabold">{{ $order->customer_name }}</span></div>
                <div><span class="text-[#475569] font-bold">WA:</span> <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_whatsapp) }}" target="_blank" rel="noopener noreferrer" class="font-bold text-[#0E7490] dark:text-[#22D3EE] underline underline-offset-2">{{ $order->customer_whatsapp }}</a></div>
                <div><span class="text-[#475569] font-bold">Tgl:</span> <span class="font-bold num">{{ $order->created_at->format('d M Y, H:i') }}</span></div>
                <div><span class="text-[#475569] font-bold">Metode:</span> <span class="font-bold">{{ $order->shipping_method ?? '-' }}</span></div>
                @if($order->customer_notes)<div><span class="text-[#475569] font-bold">Catatan:</span> <span class="font-medium">{{ $order->customer_notes }}</span></div>@endif
            </div>
        </x-card>

        <x-card>
            <h2 class="text-[12px] font-black text-[#475569] dark:text-[#D6D3D1] uppercase tracking-[0.12em] mb-3">Ringkasan</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-[#475569] font-bold">Subtotal:</span> <span class="font-extrabold num">{{ format_price($order->subtotal) }}</span></div>
                <div class="flex justify-between"><span class="text-[#475569] font-bold">Ongkir:</span> <span class="font-extrabold num">{{ format_price($order->shipping_cost) }}</span></div>
                @if(($order->down_payment ?? 0) > 0)
                    <div class="flex justify-between"><span class="text-[#475569] font-bold">DP Masuk:</span> <span class="font-extrabold text-[#166534] num">-{{ format_price($order->down_payment) }}</span></div>
                    @if(! $order->is_paid)
                        <div class="flex justify-between"><span class="text-[#475569] font-bold">Sisa Bayar:</span> <span class="font-extrabold num">{{ format_price($order->remaining) }}</span></div>
                    @endif
                @endif
                @if(($order->fee ?? 0) > 0)
                    <div class="flex justify-between"><span class="text-[#475569] font-bold">Fee Jastip:</span> <span class="font-extrabold num">{{ format_price($order->fee) }}</span></div>
                @endif
                @if(($order->discount ?? 0) > 0)
                    <div class="flex justify-between"><span class="text-[#475569] font-bold">Diskon{{ $order->coupon ? ' (' . $order->coupon->code . ')' : '' }}:</span> <span class="font-extrabold text-[#166534] num">-{{ format_price($order->discount) }}</span></div>
                @endif
                <div class="border-t-2 border-[#141414] dark:border-[#FAF6EF] pt-2 mt-2 flex justify-between font-black">
                    <span>Total</span><span class="num">{{ format_price($order->total) }}</span>
                </div>
                <div class="mt-2 flex items-center gap-2"><span class="text-[#475569] font-bold text-[13px]">Status:</span>
                    <x-badge :tone="$statusTones[$order->status] ?? 'neutral'">{{ $order->status_label }}</x-badge>
                </div>
                <div class="mt-2 flex items-center gap-2"><span class="text-[#475569] font-bold text-[13px]">Pembayaran:</span>
                    <span class="font-black uppercase text-[13px]">{{ $order->payment_method ?? 'qris' }}</span>
                    @if($order->is_paid)
                        <x-badge tone="success">Lunas</x-badge>
                    @else
                        <x-badge tone="warning">Belum bayar</x-badge>
                    @endif
                </div>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-[12px] font-black text-[#475569] dark:text-[#D6D3D1] uppercase tracking-[0.12em] mb-3">Alamat</h2>
            <div class="text-sm">
                <p class="font-bold text-[#475569]">Pengiriman:</p>
                <p class="mt-1 font-medium leading-relaxed">{{ $order->customer_address }}</p>
            </div>
        </x-card>
    </div>

    <div class="nb-table-shell">
        <div class="px-5 py-3 border-b-2 border-[#141414] dark:border-[#FAF6EF] bg-white dark:bg-[#1E1E1E]">
            <h2 class="text-[12px] font-black uppercase tracking-[0.12em]">Item Pesanan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="nb-table-head text-left text-xs uppercase">
                        <th scope="col" class="px-4 py-2.5 font-black">Produk</th>
                        <th scope="col" class="px-4 py-2.5 text-center font-black">Jumlah</th>
                        <th scope="col" class="px-4 py-2.5 text-right font-black">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-[#141414]/15 dark:divide-[#FAF6EF]/15">
                    @foreach($order->items as $item)
                        <tr class="bg-white dark:bg-[#1E1E1E]">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://placehold.co/40x40/FFC800/141414?text={{ urlencode(substr($item->product_name, 0, 1)) }}"
                                         alt="" class="w-10 h-10 rounded-[8px] border-2 border-[#141414] object-cover" loading="lazy" decoding="async">
                                    <div>
                                        <p class="font-extrabold text-sm">
                                            {{ $item->product_name }}
                                            @if(($item->status ?? 'ready') === 'po')
                                                <x-badge tone="warning" class="ml-1">PO</x-badge>
                                            @endif
                                        </p>
                                        <p class="text-xs font-bold text-[#475569]">{{ $item->unit ?? 'pcs' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-black num">x{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-sm font-black num">{{ format_price($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-wrap gap-2.5">
        @if($order->status === 'pending' || $order->status === 'awaiting_payment')
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="processing">
                <button type="submit" class="nb-btn px-4 py-2 text-sm bg-[#1D4ED8] text-white">
                    Konfirmasi Pesanan
                </button>
            </form>
        @endif
        @if($order->status === 'awaiting_payment' && ! $order->is_paid)
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="confirmed">
                <input type="hidden" name="mark_paid" value="1">
                <button type="submit" class="nb-btn px-4 py-2 text-sm bg-[#15803D] text-white">
                    Tandai Lunas dan Konfirmasi
                </button>
            </form>
        @endif
        @if(in_array($order->status, ['processing', 'ready']))
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ $order->status === 'processing' ? 'ready' : 'completed' }}">
                <button type="submit" class="nb-btn px-4 py-2 text-sm {{ $order->status === 'processing' ? 'bg-[#15803D]' : 'bg-[#0E7490]' }} text-white">
                    @if($order->status === 'processing') Tandai Siap Kirim @else Selesaikan Pesanan @endif
                </button>
            </form>
        @endif
        @if(in_array($order->status, ['pending', 'confirmed', 'processing', 'ready']))
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}"
                  class="flex flex-wrap items-center gap-2 bg-white dark:bg-[#1E1E1E] px-3 py-2 rounded-[10px] border-2 border-[#141414] dark:border-[#FAF6EF]">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="{{ \App\Models\Order::STATUS_SHIPPED }}">
                <label class="sr-only" for="resi-input">No. resi</label>
                <input id="resi-input" type="text" name="resi" value="{{ $order->resi }}" placeholder="No. resi"
                       class="nb-input px-3 py-1.5 text-sm w-40">
                <label class="sr-only" for="courier-input">Kurir</label>
                <input id="courier-input" type="text" name="courier" value="{{ $order->courier }}" placeholder="Kurir (mis. JNE)"
                       class="nb-input px-3 py-1.5 text-sm w-36">
                <button type="submit" class="nb-btn px-4 py-1.5 text-sm bg-[#0E7490] text-white">
                    <x-icon name="truck" class="w-4 h-4" /> Kirim
                </button>
            </form>
        @endif
        <form method="POST"
              action="{{ route('admin.orders.status', $order->id) }}"
              data-confirm="Batalkan pesanan ini?"
              class="inline">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="nb-btn px-4 py-2 text-sm bg-white dark:bg-[#1E1E1E] text-[#991B1B] dark:text-[#FECACA]">
                Batalkan
            </button>
        </form>
    </div>
</div>
@endsection
