<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->no }}</title>
    {{-- Template ini dirender oleh dompdf, BUKAN di browser. dompdf tidak
         mendukung flex/grid, jadi layout memakai <table> + CSS inline.
         Tanpa Tailwind (dompdf tak memuat CSS eksternal). --}}
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { margin: 0; color: #141414; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        .muted { color: #64748B; }
        .right { text-align: right; }
        .center { text-align: center; }
        .rule { border-top: 1px solid #E2E8F0; }
        .kop-rule { border-top: 3px solid #141414; }
        .items-head td { background: #141414; color: #FFFFFF; font-weight: bold; padding: 7px 8px; }
        .items td { border-bottom: 1px solid #E2E8F0; padding: 7px 8px; }
        .badge { display: inline-block; padding: 3px 9px; border: 2px solid; font-weight: bold; font-size: 10px; }
    </style>
</head>
<body>

@php
    $paid = (int) $order->down_payment + (int) $order->payments_sum;
    $left = max(0, (int) $order->total - $paid);
    $brand = setting('brand_name', 'NITIP DI END');
    $owner = setting('brand_owner');
    $storeWa = setting('whatsapp');
    $statusColor = $order->is_paid ? '#15803D' : '#B45309';
@endphp

{{-- Kop --}}
<table>
    <tr>
        <td style="vertical-align: top;">
            <div style="font-size: 20px; font-weight: bold;">{{ $brand }}</div>
            @if($owner)<div class="muted">{{ $owner }}</div>@endif
            @if($storeWa)<div class="muted">WA: {{ $storeWa }}</div>@endif
        </td>
        <td class="right" style="vertical-align: top;">
            <div style="font-size: 22px; font-weight: bold;">INVOICE</div>
            <div style="font-weight: bold;">#{{ $order->no }}</div>
            <div class="muted">{{ $order->created_at->format('d M Y, H:i') }}</div>
            <div style="margin-top: 6px;">
                <span class="badge" style="color: {{ $statusColor }}; border-color: {{ $statusColor }};">
                    {{ $order->is_paid ? 'LUNAS' : 'BELUM BAYAR' }}
                </span>
            </div>
        </td>
    </tr>
</table>

<div class="kop-rule" style="margin: 6px 0 14px;"></div>

{{-- Pelanggan & ringkasan --}}
<table>
    <tr>
        <td style="vertical-align: top; width: 55%;">
            <div class="muted" style="font-size: 10px; font-weight: bold;">DITAGIHKAN KEPADA</div>
            <div style="font-weight: bold; padding-top: 2px;">{{ $order->customer_name }}</div>
            @if($order->customer_whatsapp)<div class="muted">WA: {{ $order->customer_whatsapp }}</div>@endif
            @if($order->customer_address)<div class="muted">{{ $order->customer_address }}</div>@endif
        </td>
        <td style="vertical-align: top;">
            <div class="muted" style="font-size: 10px; font-weight: bold;">RINGKASAN</div>
            <table style="padding-top: 2px;">
                <tr><td class="muted">Ekspedisi</td><td class="right"><b>{{ $order->shipping_method ?? '-' }}</b></td></tr>
                @if($order->resi)<tr><td class="muted">Resi</td><td class="right"><b>{{ $order->resi }}</b></td></tr>@endif
                <tr><td class="muted">Jumlah item</td><td class="right"><b>{{ $order->items->sum('quantity') }} pcs</b></td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- Item --}}
<table class="items" style="margin-top: 14px;">
    <tr class="items-head">
        <td>Produk</td>
        <td>Satuan</td>
        <td class="center">Qty</td>
        <td class="right">Harga</td>
        <td class="right">Subtotal</td>
    </tr>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td class="muted">{{ $item->unit }}</td>
            <td class="center">{{ $item->quantity }}</td>
            <td class="right">{{ format_price($item->product_price) }}</td>
            <td class="right"><b>{{ format_price($item->subtotal) }}</b></td>
        </tr>
    @endforeach
</table>

{{-- Ringkasan tagihan --}}
<table style="margin-top: 14px;">
    <tr>
        <td style="width: 55%;"></td>
        <td>
            <table>
                <tr><td class="muted">Subtotal</td><td class="right">{{ format_price($order->subtotal) }}</td></tr>
                <tr><td class="muted">Ongkir</td><td class="right">{{ format_price($order->shipping_cost) }}</td></tr>
                @if(($order->fee ?? 0) > 0)
                    <tr><td class="muted">Biaya layanan</td><td class="right">{{ format_price($order->fee) }}</td></tr>
                @endif
                @if(($order->discount ?? 0) > 0)
                    <tr><td class="muted">Diskon</td><td class="right" style="color: #15803D;">-{{ format_price($order->discount) }}</td></tr>
                @endif
                <tr>
                    <td style="background: #141414; color: #FFFFFF; font-weight: bold; padding: 9px 10px;">TOTAL BAYAR</td>
                    <td class="right" style="background: #141414; color: #FFFFFF; font-weight: bold; padding: 9px 10px; font-size: 14px;">{{ format_price($order->total) }}</td>
                </tr>
                @if($left > 0)
                    <tr><td style="color: #B45309; font-weight: bold; padding-top: 6px;">Sisa</td><td class="right" style="color: #B45309; font-weight: bold; padding-top: 6px;">{{ format_price($left) }}</td></tr>
                @endif
            </table>
        </td>
    </tr>
</table>

{{-- Pembayaran --}}
<div class="rule" style="margin: 18px 0 10px;"></div>
<div class="muted">
    @if($order->is_paid)
        <b style="color: #15803D;">Pembayaran diterima.</b> Terima kasih{{ $order->paid_at ? ', '.$order->paid_at->format('d M Y, H:i') : '' }}.
    @else
        Silakan transfer, lalu kirim bukti via WhatsApp{{ $storeWa ? ' ('.$storeWa.')' : '' }} dengan menyebut nomor invoice <b style="color: #141414;">#{{ $order->no }}</b>.
    @endif
</div>

<div class="rule" style="margin: 14px 0 8px;"></div>
<div class="center muted">Terima kasih telah menitip di {{ $brand }}.</div>

</body>
</html>