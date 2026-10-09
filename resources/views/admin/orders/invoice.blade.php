<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} | {{ setting('brand_name', 'NITIP DI END') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .sheet { box-shadow: none !important; border: none !important; margin: 0 !important; }
            @page { margin: 12mm; }
        }
    </style>
</head>
<body class="bg-[#F1F5F9] text-[#1E293B] antialiased">

@php
    $brand    = setting('brand_name', 'NITIP DI END');
    $owner    = setting('brand_owner');
    $address  = setting('address');
    $whatsapp = setting('whatsapp');
@endphp

{{-- Toolbar (tidak tercetak) --}}
<div class="no-print sticky top-0 z-10 bg-white border-b border-[#E2E8F0]">
    <div class="max-w-[820px] mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-orange-600 hover:underline">&larr; Kembali ke pesanan</a>
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#0891B2] hover:bg-[#0E7490] text-white text-sm font-medium rounded-lg shadow-sm">
            Cetak / Simpan PDF
        </button>
    </div>
</div>

{{-- Lembar invoice --}}
<div class="sheet max-w-[820px] mx-auto bg-white my-6 p-8 md:p-10 shadow-sm border border-[#E2E8F0]">

    {{-- Kop --}}
    <div class="flex items-start justify-between gap-6 pb-6 border-b border-[#E2E8F0]">
        <div>
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-full bg-[#0891B2] flex items-center justify-center text-white font-bold text-xl">
                    {{ strtoupper(substr($brand, 0, 1)) }}
                </div>
                <div>
                    <div class="text-lg font-bold leading-tight">{{ $brand }}</div>
                    @if($owner)<div class="text-xs text-[#64748B]">{{ $owner }}</div>@endif
                </div>
            </div>
            <div class="mt-3 text-xs text-[#64748B] space-y-0.5">
                @if($address)<div>{{ $address }}</div>@endif
                @if($whatsapp)<div>WA: {{ $whatsapp }}</div>@endif
            </div>
        </div>
        <div class="text-right shrink-0">
            <div class="text-2xl font-bold tracking-wide text-[#0891B2]">INVOICE</div>
            <div class="text-sm font-semibold mt-1">#{{ $order->order_number }}</div>
            <div class="text-xs text-[#64748B] mt-1">Tanggal: {{ $order->created_at->format('d M Y, H:i') }}</div>
            <div class="mt-2">
                @if($order->is_paid)
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">LUNAS</span>
                @else
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">BELUM BAYAR</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Tagihan untuk --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-6 text-sm">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">Tagihan untuk</div>
            <div class="font-medium">{{ $order->customer_name }}</div>
            @if($order->customer_whatsapp)<div class="text-[#64748B]">HP/WA: {{ $order->customer_whatsapp }}</div>@endif
            @if($order->customer_address)<div class="text-[#64748B]">Alamat: {{ $order->customer_address }}</div>@endif
        </div>
        <div class="sm:text-right">
            <div class="text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">Pengiriman</div>
            <div>Jasa ekspedisi: <span class="font-medium">{{ $order->shipping_method ?? '-' }}</span></div>
            @if($order->resi)<div class="text-[#64748B]">No. resi: {{ $order->resi }}</div>@endif
            <div class="text-[#64748B]">Ongkir: {{ format_price($order->shipping_cost) }}</div>
        </div>
    </div>
{{-- Produk --}}
    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="bg-[#F8FAFC] text-[#64748B] text-left">
                <th class="py-2.5 px-3 font-semibold">Produk</th>
                <th class="py-2.5 px-3 font-semibold">Satuan</th>
                <th class="py-2.5 px-3 font-semibold text-center">Jumlah</th>
                <th class="py-2.5 px-3 font-semibold text-right">Harga</th>
                <th class="py-2.5 px-3 font-semibold text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $item)
                <tr class="border-b border-[#E2E8F0]">
                    <td class="py-2.5 px-3">{{ $item->product_name }}</td>
                    <td class="py-2.5 px-3 text-[#64748B]">{{ $item->unit }}</td>
                    <td class="py-2.5 px-3 text-center">{{ $item->quantity }}</td>
                    <td class="py-2.5 px-3 text-right">{{ format_price($item->product_price) }}</td>
                    <td class="py-2.5 px-3 text-right font-medium">{{ format_price($item->subtotal) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 px-3 text-center text-[#94A3B8]">Tidak ada item.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <div class="flex justify-end mt-6">
        <div class="w-full sm:w-72 text-sm space-y-1.5">
            <div class="flex justify-between"><span class="text-[#64748B]">Subtotal</span><span>{{ format_price($order->subtotal) }}</span></div>
            <div class="flex justify-between"><span class="text-[#64748B]">Ongkir</span><span>{{ format_price($order->shipping_cost) }}</span></div>
            @if(($order->fee ?? 0) > 0)
                <div class="flex justify-between"><span class="text-[#64748B]">Biaya Fee</span><span>{{ format_price($order->fee) }}</span></div>
            @endif
            @if(($order->discount ?? 0) > 0)
                <div class="flex justify-between"><span class="text-[#64748B]">Diskon{{ $order->coupon ? ' (' . $order->coupon->code . ')' : '' }}</span><span class="text-emerald-600">-{{ format_price($order->discount) }}</span></div>
            @endif
            <div class="flex justify-between pt-2 mt-1 border-t border-[#E2E8F0] text-base font-bold">
                <span>Total Bayar</span><span class="text-[#0891B2]">{{ format_price($order->total) }}</span>
            </div>
        </div>
    </div>
{{-- Pembayaran --}}
    <div class="mt-8 pt-6 border-t border-[#E2E8F0]">
        @if($order->is_paid)
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 flex flex-col sm:flex-row items-center gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2 text-emerald-800 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Pembayaran diterima
                    </div>
                    <p class="text-sm text-emerald-700 mt-1">
                        Terima kasih, pembayaran pesanan ini sudah kami terima
                        @if($order->paid_at) pada {{ $order->paid_at->format('d M Y, H:i') }}@endif.
                    </p>
                </div>
                @if($paidImage)
                    <img src="{{ $paidImage }}" alt="Pembayaran diterima" class="w-52 h-auto rounded-lg border border-emerald-200 bg-white">
                @endif
            </div>
        @else
            <div class="text-sm font-semibold mb-3">Cara pembayaran</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- QRIS --}}
                <div class="rounded-xl border border-[#E2E8F0] p-4 text-center">
                    <div class="text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">Bayar via QRIS</div>
                    @if($qrisImage)
                        <img src="{{ $qrisImage }}" alt="QRIS {{ $qrisMerchant }}" class="mx-auto w-44 h-auto rounded border border-[#E2E8F0]">
                        <div class="text-xs text-[#64748B] mt-2">{{ $qrisMerchant }}</div>
                    @else
                        <p class="text-xs text-[#94A3B8] italic py-8">QRIS belum diunggah. Atur di halaman Pengaturan &rarr; QRIS.</p>
                    @endif
                </div>
                {{-- Transfer rekening --}}
                <div class="rounded-xl border border-[#E2E8F0] p-4 text-center">
                    <div class="text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-2">atau Transfer Rekening</div>
                    @if($rekeningImage)
                        <img src="{{ $rekeningImage }}" alt="Rekening pembayaran" class="mx-auto w-44 h-auto rounded border border-[#E2E8F0]">
                    @else
                        <p class="text-xs text-[#94A3B8] italic py-8">Rekening belum diunggah.</p>
                    @endif
                </div>
            </div>
            <p class="text-xs text-[#64748B] mt-3">Setelah transfer, kirim bukti pembayaran lewat WhatsApp <span class="font-medium">{{ $whatsapp ?: $owner }}</span> dengan menyebut nomor invoice <span class="font-medium">#{{ $order->order_number }}</span>.</p>
        @endif
    </div>

    <div class="mt-8 pt-4 border-t border-[#E2E8F0] text-center text-xs text-[#94A3B8]">
        Terima kasih telah menitip di {{ $brand }}.
    </div>
</div>

@if($autoPrint)
    <script>window.addEventListener('load', () => window.print());</script>
@endif
</body>
</html>