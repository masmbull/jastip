{{--
  TEMPLATE INVOICE TUNGGAL.
  Dipakai oleh DUA tempat sekaligus supaya pratinjau == PDF:
    - resources/views/invoices/pdf.blade.php   (dirender dompdf)
    - resources/views/admin/invoices/sample.blade.php (pratinjau di browser)
  Karena itu isinya WAJIB:
    - memakai <table> + CSS inline (dompdf tidak mendukung flex/grid),
    - tanpa utility Tailwind (dompdf tidak memuat CSS eksternal),
    - semua harga sudah diformat (string) oleh pemanggil.

  Variabel: $doc (array) dari InvoiceController::buildDoc() / sampleInvoice().
--}}
<div class="invoice-doc" style="font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #141414; font-size: 12px; background: #FFFFFF; width: 100%;">

    {{-- Kop --}}
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top;">
                <div style="font-size:20px; font-weight:bold;">{{ $doc['brand'] }}</div>
                @if($doc['owner'])<div style="color:#475569;">{{ $doc['owner'] }}</div>@endif
                @if($doc['store_wa'])<div style="color:#475569;">WA: {{ $doc['store_wa'] }}</div>@endif
            </td>
            <td style="vertical-align:top; text-align:right;">
                <div style="font-size:22px; font-weight:bold;">{{ $doc['title'] }}</div>
                <div style="font-weight:bold;">#{{ $doc['number'] }}</div>
                <div style="color:#475569;">{{ $doc['date'] }}</div>
                <div style="margin-top:6px;">
                    <span style="display:inline-block; padding:3px 9px; border:2px solid {{ $doc['status_color'] }}; color:{{ $doc['status_color'] }}; font-weight:bold; font-size:10px;">{{ $doc['status_text'] }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div style="border-top:3px solid #141414; margin:6px 0 14px;"></div>

    {{-- Pelanggan & ringkasan --}}
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top; width:55%;">
                <div style="color:#475569; font-size:10px; font-weight:bold;">DITAGIHKAN KEPADA</div>
                <div style="font-weight:bold; padding-top:2px;">{{ $doc['customer']['name'] }}</div>
                @if($doc['customer']['wa'])<div style="color:#475569;">WA: {{ $doc['customer']['wa'] }}</div>@endif
                @if($doc['customer']['address'])<div style="color:#475569;">{{ $doc['customer']['address'] }}</div>@endif
            </td>
            <td style="vertical-align:top;">
                <div style="color:#475569; font-size:10px; font-weight:bold;">RINGKASAN</div>
                <table style="width:100%; border-collapse:collapse; padding-top:2px;">
                    @foreach($doc['shipping'] as $row)
                        <tr>
                            <td style="color:#475569;">{{ $row['label'] }}</td>
                            <td style="text-align:right; font-weight:bold;">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- Item --}}
    <table style="width:100%; border-collapse:collapse; margin-top:14px;">
        <tr>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:7px 8px;">Produk</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:7px 8px;">Satuan</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:7px 8px; text-align:center;">Qty</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:7px 8px; text-align:right;">Harga</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:7px 8px; text-align:right;">Subtotal</td>
        </tr>
        @foreach($doc['items'] as $item)
            <tr>
                <td style="border-bottom:1px solid #E2E8F0; padding:7px 8px;">{{ $item['name'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:7px 8px; color:#475569;">{{ $item['unit'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:7px 8px; text-align:center;">{{ $item['qty'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:7px 8px; text-align:right;">{{ $item['price'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:7px 8px; text-align:right; font-weight:bold;">{{ $item['subtotal'] }}</td>
            </tr>
        @endforeach
    </table>

    {{-- Total --}}
    <table style="width:100%; border-collapse:collapse; margin-top:14px;">
        <tr>
            <td style="width:55%;"></td>
            <td>
                <table style="width:100%; border-collapse:collapse;">
                    @foreach($doc['totals'] as $row)
                        <tr>
                            <td style="color:#475569; padding:2px 0;">{{ $row['label'] }}</td>
                            <td style="text-align:right; padding:2px 0; @if(!empty($row['muted'])) color:#15803D; @endif">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:9px 10px;">{{ $doc['total']['label'] }}</td>
                        <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:9px 10px; text-align:right; font-size:14px;">{{ $doc['total']['value'] }}</td>
                    </tr>
                    @if($doc['left'])
                        <tr>
                            <td style="color:#B45309; font-weight:bold; padding-top:6px;">{{ $doc['left']['label'] }}</td>
                            <td style="text-align:right; color:#B45309; font-weight:bold; padding-top:6px;">{{ $doc['left']['value'] }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Pembayaran --}}
    <div style="border-top:1px solid #E2E8F0; margin:18px 0 10px;"></div>
    <div style="color:#475569;">{{ $doc['payment_text'] }}</div>

    <div style="border-top:1px solid #E2E8F0; margin:14px 0 8px;"></div>
    <div style="text-align:center; color:#475569;">{{ $doc['footer'] }}</div>

</div>