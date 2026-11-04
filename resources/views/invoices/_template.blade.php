{{--
  Shared by the browser preview, customer invoice, PDF, and WhatsApp image.
  Keep the table layout and inline CSS compatible with Dompdf.
  $doc is prepared by InvoiceController.
--}}
<div class="invoice-doc" style="position:relative; font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #141414; font-size: 14px; background: #FFFFFF; width: 100%;">

    {{-- Stempel PAID kalau invoice sudah lunas. --}}
    @if($doc['paid'] ?? false)
        <div style="position:absolute; top:110px; right:40px; transform:rotate(-16deg); border:4px solid #15803D; color:#15803D; padding:6px 18px; font-size:26px; font-weight:bold; letter-spacing:6px; opacity:.85;" aria-label="Sudah dibayar">PAID</div>
    @endif

    {{-- Kop --}}
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top; padding-bottom:14px; border-bottom:3px solid #141414;">
                @if(!empty($doc['logo']))
                    <img src="{{ $doc['logo'] }}" alt="{{ $doc['brand'] }}" style="display:block; max-width:210px; max-height:64px; object-fit:contain; margin-bottom:8px;">
                @endif
                <div style="font-size:24px; font-weight:bold;">{{ $doc['brand'] }}</div>
                @if($doc['owner'])<div style="color:#334155; font-size:14px;">{{ $doc['owner'] }}</div>@endif
                @if($doc['store_wa'])<div style="color:#334155; font-size:14px;">WA: {{ $doc['store_wa'] }}</div>@endif
            </td>
            <td style="vertical-align:top; text-align:right; padding-bottom:14px; border-bottom:3px solid #141414;">
                <div style="display:inline-block; background:#FFC800; border:2px solid #141414; padding:5px 10px; font-size:26px; font-weight:bold;">{{ $doc['title'] }}</div>
                <div style="font-size:15px; font-weight:bold; margin-top:5px;">#{{ $doc['number'] }}</div>
                <div style="color:#334155; font-size:13px;">{{ $doc['date'] }}</div>
                <div style="margin-top:6px;">
                    <span style="display:inline-block; padding:4px 9px; border:2px solid {{ $doc['status_color'] }}; color:{{ $doc['status_color'] }}; font-weight:bold; font-size:11px;">{{ $doc['status_text'] }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Pelanggan & ringkasan --}}
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top; width:55%; background:#FAF6EF; border:2px solid #141414; padding:12px;">
                <div style="color:#334155; font-size:11px; font-weight:bold;">DITAGIHKAN KEPADA</div>
                <div style="font-weight:bold; padding-top:4px; font-size:15px;">{{ $doc['customer']['name'] }}</div>
                @if($doc['customer']['wa'])<div style="color:#334155; font-size:13px;">WA: {{ $doc['customer']['wa'] }}</div>@endif
                @if($doc['customer']['address'])<div style="color:#334155; font-size:13px;">{{ $doc['customer']['address'] }}</div>@endif
            </td>
            <td style="vertical-align:top; background:#FAF6EF; border:2px solid #141414; padding:12px;">
                <div style="color:#334155; font-size:11px; font-weight:bold;">RINGKASAN</div>
                <table style="width:100%; border-collapse:collapse; padding-top:4px;">
                    @foreach($doc['shipping'] as $row)
                        <tr>
                            <td style="color:#334155; font-size:13px;">{{ $row['label'] }}</td>
                            <td style="text-align:right; font-weight:bold;">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- Item --}}
    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <tr>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:9px 8px;">Produk</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:9px 8px;">Satuan</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:9px 8px; text-align:center;">Qty</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:9px 8px; text-align:right;">Harga</td>
            @if(!empty($doc['item_has_fee']))
                <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:9px 8px; text-align:right;">Fee item</td>
            @endif
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:9px 8px; text-align:right;">Subtotal</td>
        </tr>
        @foreach($doc['items'] as $item)
            <tr>
                <td style="border-bottom:1px solid #C2BDB2; padding:9px 8px; font-size:13px;">{{ $item['name'] }}</td>
                <td style="border-bottom:1px solid #C2BDB2; padding:9px 8px; color:#334155; font-size:13px;">{{ $item['unit'] }}</td>
                <td style="border-bottom:1px solid #C2BDB2; padding:9px 8px; text-align:center; font-size:13px;">{{ $item['qty'] }}</td>
                <td style="border-bottom:1px solid #C2BDB2; padding:9px 8px; text-align:right; font-size:13px;">{{ $item['price'] }}</td>
                @if(!empty($doc['item_has_fee']))
                    <td style="border-bottom:1px solid #C2BDB2; padding:9px 8px; text-align:right; color:#78350F; font-size:13px;">
                        {{ $item['fee'] ?? '-' }}
                    </td>
                @endif
                <td style="border-bottom:1px solid #C2BDB2; padding:9px 8px; text-align:right; font-weight:bold; font-size:13px;">{{ $item['subtotal'] }}</td>
            </tr>
        @endforeach
    </table>

    {{-- Total --}}
    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <tr>
            <td style="width:55%;"></td>
            <td>
                <table style="width:100%; border-collapse:collapse;">
                    @foreach($doc['totals'] as $row)
                        <tr>
                            <td style="color:#334155; padding:3px 0; font-size:13px;">{{ $row['label'] }}</td>
                            <td style="text-align:right; padding:3px 0; font-size:13px; @if(!empty($row['green'])) color:#166534; font-weight:bold; @elseif(!empty($row['muted'])) color:#334155; @endif">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="background:#FFC800; color:#141414; border:2px solid #141414; font-weight:bold; padding:10px;">{{ $doc['total']['label'] }}</td>
                        <td style="background:#FFC800; color:#141414; border:2px solid #141414; font-weight:bold; padding:10px; text-align:right; font-size:17px;">{{ $doc['total']['value'] }}</td>
                    </tr>
                    @if($doc['left'])
                        <tr>
                            <td style="color:#B45309; font-weight:bold; padding-top:6px;">{{ $doc['left']['label'] }}</td>
                            <td style="text-align:right; color:#B45309; font-weight:bold; font-size:14px; padding-top:6px;">{{ $doc['left']['value'] }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Pembayaran: QRIS + rekening.
         QRIS hanya muncul kalau diizinkan untuk nominal ini (<= batas);
         di atas batas, hanya rekening. --}}
    <div style="border-top:2px solid #141414; margin:18px 0 10px;"></div>
    <div style="font-size:12px; font-weight:bold; color:#141414; margin-bottom:8px;">CARA PEMBAYARAN</div>

    @php($qris = $doc['qris'] ?? null)
    @php($bank = $doc['bank'] ?? null)

    @if($doc['paid'] ?? false)
        <div style="font-size:12px; color:#15803D; font-weight:bold;">
            Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada '.$doc['paid_at'] : '' }}. Terima kasih.
        </div>
    @elseif($qris || ($bank && !empty($bank['lines'])))
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                @if($qris)
                    <td style="vertical-align:top; text-align:center; width:50%; background:#FAF6EF; border:2px solid #141414; padding:12px;">
                        <div style="font-weight:bold; font-size:13px; margin-bottom:6px;">QRIS</div>
                        @if(!empty($qris['image']))
                            <img src="{{ $qris['image'] }}" alt="QRIS" style="width:170px; height:auto;">
                        @endif
                        @if(!empty($qris['merchant']))
                            <div style="color:#334155; font-size:12px; margin-top:4px;">{{ $qris['merchant'] }}</div>
                        @endif
                    </td>
                @endif

                @if($bank && !empty($bank['lines']))
                    <td style="vertical-align:top; {{ $qris ? 'padding-left:12px;' : '' }} background:#FAF6EF; border:2px solid #141414; padding:12px;">
                        <div style="font-weight:bold; font-size:13px; margin-bottom:6px;">TRANSFER REKENING</div>
                        @foreach($bank['lines'] as $line)
                            <div style="font-size:14px; {{ $loop->first ? 'font-weight:bold;' : 'color:#334155;' }}">{{ $line }}</div>
                        @endforeach
                    </td>
                @endif
            </tr>
        </table>

        <div style="margin-top:8px; padding:8px; background:#FFC800; border:2px solid #141414; font-size:12px; color:#141414; font-weight:bold;">
            TRANSFER HANYA KE REKENING / QRIS DI ATAS. SELAIN ITU DI LUAR TANGGUNG JAWAB KAMI.
        </div>
    @else
        <div style="color:#475569;">{{ $doc['payment_text'] }}</div>
    @endif

    <div style="border-top:2px solid #141414; margin:16px 0 10px;"></div>
    <div style="text-align:center; color:#334155; font-size:13px;">{{ $doc['footer'] }}</div>

</div>