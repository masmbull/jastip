<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->no }} - gambar</title>
</head>
{{-- Halaman ini SENGAJA memakai warna hex inline, tanpa utility Tailwind.
     Tujuannya: dirender ke PNG oleh html2canvas di sisi klien (untuk dikirim
     sebagai gambar lewat WhatsApp). Tailwind v4 memakai color(oklch ...) yang
     tidak dipahami html2canvas, jadi halaman target render wajib hex murni. --}}
@php
    $paid = (int) $order->down_payment + (int) $order->payments_sum;
    $left = max(0, (int) $order->total - $paid);
    $brand = setting('brand_name', 'NITIP DI END');
    $owner = setting('brand_owner');
    $storeWa = setting('whatsapp');

    $statusText = $order->is_paid ? 'LUNAS' : 'BELUM BAYAR';
    $statusColor = $order->is_paid ? '#15803D' : '#B45309';

    $paymentText = $order->is_paid
        ? 'Pembayaran diterima. Terima kasih' . ($order->paid_at ? ', ' . $order->paid_at->format('d M Y, H:i') . '.' : '.')
        : 'Silakan transfer, lalu kirim bukti via WhatsApp'
            . ($storeWa ? ' (' . $storeWa . ')' : '')
            . ' dengan menyebut nomor invoice #' . $order->no . '.';
@endphp

<body style="margin:0;padding:0;background:#EEF2F6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#141414;">

<div id="invoice-canvas" style="width:720px;margin:0 auto;background:#FFFFFF;padding:40px;box-sizing:border-box;">

    {{-- Kop --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #141414;padding-bottom:18px;">
        <div>
            <div style="font-size:22px;font-weight:800;letter-spacing:0.02em;">{{ $brand }}</div>
            @if($owner)
                <div style="font-size:12px;color:#64748B;margin-top:2px;">{{ $owner }}</div>
            @endif
            @if($storeWa)
                <div style="font-size:12px;color:#64748B;margin-top:2px;">WA: {{ $storeWa }}</div>
            @endif
        </div>
        <div style="text-align:right;">
            <div style="font-size:26px;font-weight:800;letter-spacing:0.04em;">INVOICE</div>
            <div style="font-size:13px;font-weight:700;margin-top:4px;">#{{ $order->no }}</div>
            <div style="font-size:12px;color:#64748B;">{{ $order->created_at->format('d M Y, H:i') }}</div>
            <div style="display:inline-block;margin-top:8px;padding:4px 10px;border:2px solid {{ $statusColor }};color:{{ $statusColor }};font-size:11px;font-weight:800;">{{ $statusText }}</div>
        </div>
    </div>

    {{-- Pelanggan --}}
    <div style="display:flex;justify-content:space-between;gap:24px;padding:18px 0;">
        <div style="font-size:13px;">
            <div style="color:#64748B;font-size:11px;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Ditagihkan kepada</div>
            <div style="font-weight:700;">{{ $order->customer_name }}</div>
            @if($order->customer_whatsapp)
                <div style="color:#64748B;">WA: {{ $order->customer_whatsapp }}</div>
            @endif
            @if($order->customer_address)
                <div style="color:#64748B;">{{ $order->customer_address }}</div>
            @endif
        </div>
        <div style="font-size:13px;text-align:right;">
            <div style="color:#64748B;font-size:11px;font-weight:700;text-transform:uppercase;margin-bottom:4px;">Ringkasan</div>
            <div>Ekspedisi: <strong>{{ $order->shipping_method ?? '-' }}</strong></div>
            @if($order->resi)
                <div>Resi: <strong>{{ $order->resi }}</strong></div>
            @endif
            <div>Jumlah item: <strong>{{ $order->items->sum('quantity') }} pcs</strong></div>
        </div>
    </div>

    {{-- Item --}}
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:#141414;color:#FFFFFF;text-align:left;">
                <th style="padding:8px 10px;">Produk</th>
                <th style="padding:8px 10px;">Satuan</th>
                <th style="padding:8px 10px;text-align:center;">Qty</th>
                <th style="padding:8px 10px;text-align:right;">Harga</th>
                <th style="padding:8px 10px;text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr style="border-bottom:1px solid #E2E8F0;">
                    <td style="padding:8px 10px;">{{ $item->product_name }}</td>
                    <td style="padding:8px 10px;color:#64748B;">{{ $item->unit }}</td>
                    <td style="padding:8px 10px;text-align:center;">{{ $item->quantity }}</td>
                    <td style="padding:8px 10px;text-align:right;">{{ format_price($item->product_price) }}</td>
                    <td style="padding:8px 10px;text-align:right;font-weight:700;">{{ format_price($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <div style="display:flex;justify-content:flex-end;margin-top:16px;">
        <div style="width:300px;font-size:13px;">
            <div style="display:flex;justify-content:space-between;padding:3px 0;"><span style="color:#64748B;">Subtotal</span><span>{{ format_price($order->subtotal) }}</span></div>
            <div style="display:flex;justify-content:space-between;padding:3px 0;"><span style="color:#64748B;">Ongkir</span><span>{{ format_price($order->shipping_cost) }}</span></div>
            @if(($order->fee ?? 0) > 0)
                <div style="display:flex;justify-content:space-between;padding:3px 0;"><span style="color:#64748B;">Biaya layanan</span><span>{{ format_price($order->fee) }}</span></div>
            @endif
            @if(($order->discount ?? 0) > 0)
                <div style="display:flex;justify-content:space-between;padding:3px 0;"><span style="color:#64748B;">Diskon</span><span style="color:#15803D;">-{{ format_price($order->discount) }}</span></div>
            @endif
            <div style="display:flex;justify-content:space-between;margin-top:8px;padding:10px 12px;background:#141414;color:#FFFFFF;font-weight:800;font-size:15px;">
                <span>TOTAL BAYAR</span><span>{{ format_price($order->total) }}</span>
            </div>
            @if($left > 0)
                <div style="display:flex;justify-content:space-between;padding:6px 2px;font-weight:700;color:#B45309;">
                    <span>Sisa</span><span>{{ format_price($left) }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Pembayaran --}}
    <div style="margin-top:22px;padding-top:14px;border-top:1px solid #E2E8F0;font-size:12px;color:#64748B;">{{ $paymentText }}</div>

    <div style="margin-top:24px;padding-top:14px;border-top:1px solid #E2E8F0;text-align:center;font-size:12px;color:#64748B;">Terima kasih telah menitip di {{ $brand }}.</div>
</div>
</body>
</html>