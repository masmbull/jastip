<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Konfirmasi Pesanan</title>
</head>
<body style="margin:0;padding:0;background:#ecfeff;font-family:Arial,Helvetica,sans-serif;color:#1E293B;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ecfeff;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background:#06B6D4;padding:20px 24px;color:#ffffff;font-size:20px;font-weight:bold;">
                            {{ setting('brand_name', 'NITIP DI END') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <h1 style="margin:0 0 12px;font-size:20px;">Pesanan {{ $order->order_number }} diterima</h1>
                            <p style="margin:0 0 16px;color:#64748B;">Halo {{ $order->customer_name }}, pesanan kamu sudah kami terima. Selesaikan pembayaran via QRIS agar segera diproses.</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                <tr><td style="padding:6px 0;color:#64748B;">Subtotal</td><td align="right">{{ $order->formatted_subtotal }}</td></tr>
                                <tr><td style="padding:6px 0;color:#64748B;">Ongkir</td><td align="right">{{ format_price($order->shipping_cost) }}</td></tr>
                                @if($order->fee > 0)
                                <tr><td style="padding:6px 0;color:#64748B;">Biaya Fee</td><td align="right">{{ $order->formatted_fee }}</td></tr>
                                @endif
                                @if($order->discount > 0)
                                <tr><td style="padding:6px 0;color:#64748B;">Diskon{{ $order->coupon ? ' (' . $order->coupon->code . ')' : '' }}</td><td align="right" style="color:#16A34A;">-{{ $order->formatted_discount }}</td></tr>
                                @endif
                                <tr><td style="padding:10px 0;border-top:1px solid #E2E8F0;font-weight:bold;">Total Bayar</td><td align="right" style="padding:10px 0;border-top:1px solid #E2E8F0;font-weight:bold;color:#06B6D4;">{{ $order->formatted_total }}</td></tr>
                            </table>

                            <h2 style="margin:24px 0 8px;font-size:15px;">Produk Dipesan</h2>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                @foreach($order->items as $item)
                                <tr><td style="padding:4px 0;">{{ $item->product_name }} x{{ $item->quantity }}</td><td align="right">{{ format_price($item->subtotal) }}</td></tr>
                                @endforeach
                            </table>

                            <p style="margin:24px 0 0;color:#64748B;font-size:13px;">Cek status pesanan: <a href="{{ route('order.show', $order->order_number) }}" style="color:#06B6D4;">{{ route('order.show', $order->order_number) }}</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;background:#F1F5F9;color:#94A3B8;font-size:12px;">
                            Terima kasih telah menitip di {{ setting('brand_name', 'NITIP DI END') }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
