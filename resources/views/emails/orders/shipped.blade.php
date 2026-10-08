<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pesanan Dikirim</title>
</head>
<body style="margin:0;padding:0;background:#FDF6EC;font-family:Arial,Helvetica,sans-serif;color:#1E293B;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FDF6EC;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background:#fb923c;padding:20px 24px;color:#ffffff;font-size:20px;font-weight:bold;">
                            {{ setting('brand_name', 'NITIP DI END') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <h1 style="margin:0 0 12px;font-size:20px;">Pesanan {{ $order->order_number }} sudah dikirim</h1>
                            <p style="margin:0 0 16px;color:#64748B;">Halo {{ $order->customer_name }}, pesanan kamu sedang dalam perjalanan.</p>

                            <h2 style="margin:16px 0 8px;font-size:15px;">Produk Dikirim</h2>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                @foreach($order->items as $item)
                                <tr><td style="padding:4px 0;">{{ $item->product_name }} x{{ $item->quantity }}</td><td align="right">{{ format_price($item->subtotal) }}</td></tr>
                                @endforeach
                            </table>

                            <p style="margin:16px 0 0;color:#64748B;font-size:14px;">Total: <strong>{{ $order->formatted_total }}</strong></p>
                            <p style="margin:8px 0 0;color:#64748B;font-size:13px;">Alamat: {{ $order->customer_address }}</p>

                            <p style="margin:24px 0 0;color:#64748B;font-size:13px;">Cek status pesanan: <a href="{{ route('order.show', $order->order_number) }}" style="color:#fb923c;">{{ route('order.show', $order->order_number) }}</a></p>
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
