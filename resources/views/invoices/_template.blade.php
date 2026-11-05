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
<div class="invoice-doc" style="position:relative; font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #141414; font-size: 9px; line-height:1.15; background: #FFFFFF; width: 100%;">

    {{-- Stempel PAID kalau invoice sudah lunas. --}}
    @if($doc['paid'] ?? false)
        <div style="position:absolute; top:110px; right:40px; transform:rotate(-16deg); border:4px solid #15803D; color:#15803D; padding:6px 18px; font-size:26px; font-weight:bold; letter-spacing:6px; opacity:.85;" aria-label="Sudah dibayar">PAID</div>
    @endif

    {{-- Kop --}}
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top;">
                @if(!empty($doc['logo']))
                    <img src="{{ $doc['logo'] }}" alt="{{ $doc['brand'] }}" style="display:block; max-width:150px; max-height:42px; object-fit:contain; margin-bottom:3px;">
                @endif
                <div style="font-size:18px; font-weight:bold;">{{ $doc['brand'] }}</div>
                @if($doc['owner'])<div style="color:#475569;">{{ $doc['owner'] }}</div>@endif
                @if($doc['store_wa'])<div style="color:#475569;">WA: {{ $doc['store_wa'] }}</div>@endif
            </td>
            <td style="vertical-align:top; text-align:right;">
                <div style="font-size:19px; font-weight:bold;">{{ $doc['title'] }}</div>
                <div style="font-weight:bold;">#{{ $doc['number'] }}</div>
                <div style="color:#475569;">{{ $doc['date'] }}</div>
                <div style="margin-top:3px;">
                    <span style="display:inline-block; padding:2px 7px; border:2px solid {{ $doc['status_color'] }}; color:{{ $doc['status_color'] }}; font-weight:bold; font-size:9px;">{{ $doc['status_text'] }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div style="border-top:3px solid #141414; margin:3px 0 6px;"></div>

    {{-- Pelanggan & ringkasan --}}
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top; width:55%;">
                <div style="color:#475569; font-size:9px; font-weight:bold;">DITAGIHKAN KEPADA</div>
                <div style="font-weight:bold; padding-top:2px;">{{ $doc['customer']['name'] }}</div>
                @if($doc['customer']['wa'])<div style="color:#475569;">WA: {{ $doc['customer']['wa'] }}</div>@endif
                @if($doc['customer']['address'])<div style="color:#475569;">{{ $doc['customer']['address'] }}</div>@endif
            </td>
            <td style="vertical-align:top;">
                <div style="color:#475569; font-size:9px; font-weight:bold;">RINGKASAN</div>
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
    <table style="width:100%; border-collapse:collapse; margin-top:5px;">
        <tr>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:3px 4px;">Produk</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:3px 4px;">Satuan</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:3px 4px; text-align:center;">Qty</td>
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:3px 4px; text-align:right;">Harga</td>
            @if(!empty($doc['item_has_fee']))
                <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:3px 4px; text-align:right;">Fee item</td>
            @endif
            <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:3px 4px; text-align:right;">Subtotal</td>
        </tr>
        @foreach($doc['items'] as $item)
            <tr>
                <td style="border-bottom:1px solid #E2E8F0; padding:2px 4px;">{{ $item['name'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:2px 4px; color:#475569;">{{ $item['unit'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:2px 4px; text-align:center;">{{ $item['qty'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:2px 4px; text-align:right;">{{ $item['price'] }}</td>
                @if(!empty($doc['item_has_fee']))
                    <td style="border-bottom:1px solid #E2E8F0; padding:2px 4px; text-align:right; color:#92400E;">
                        {{ $item['fee'] ?? '-' }}
                    </td>
                @endif
                <td style="border-bottom:1px solid #E2E8F0; padding:2px 4px; text-align:right; font-weight:bold;">{{ $item['subtotal'] }}</td>
            </tr>
        @endforeach
    </table>

    {{-- Total --}}
    <table style="width:100%; border-collapse:collapse; margin-top:5px;">
        <tr>
            <td style="width:55%;"></td>
            <td class="invoice-totals-box" style="border:2px solid #141414; padding:3px;">
                <table style="width:100%; border-collapse:collapse;">
                    @foreach($doc['totals'] as $row)
                        <tr>
                            <td style="color:#475569; padding:2px;">{{ $row['label'] }}</td>
                            <td style="text-align:right; padding:2px; @if(!empty($row['green'])) color:#166534; font-weight:bold; @elseif(!empty($row['muted'])) color:#475569; @endif">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:4px 6px; border-top:2px solid #141414;">{{ $doc['total']['label'] }}</td>
                        <td style="background:#141414; color:#FFFFFF; font-weight:bold; padding:4px 6px; text-align:right; font-size:12px; border-top:2px solid #141414;">{{ $doc['total']['value'] }}</td>
                    </tr>
                    @if($doc['left'])
                        <tr>
                            <td style="color:#B45309; font-weight:bold; padding:3px 2px 2px;">{{ $doc['left']['label'] }}</td>
                            <td style="text-align:right; color:#B45309; font-weight:bold; font-size:12px; padding:3px 2px 2px;">{{ $doc['left']['value'] }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Pembayaran: QRIS + rekening.
         QRIS hanya muncul kalau diizinkan untuk nominal ini (<= batas);
         di atas batas, hanya rekening. --}}
    <div style="border-top:1px solid #E2E8F0; margin:6px 0 3px;"></div>
    <div style="font-size:9px; font-weight:bold; color:#475569; margin-bottom:2px;">CARA PEMBAYARAN</div>

    @php($qris = $doc['qris'] ?? null)
    @php($bank = $doc['bank'] ?? null)

    @if($doc['paid'] ?? false)
        <div style="font-size:10px; color:#15803D; font-weight:bold;">
            Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada '.$doc['paid_at'] : '' }}. Terima kasih.
        </div>
    @elseif($qris || ($bank && !empty($bank['lines'])))
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                @if($qris)
                    <td style="vertical-align:top; text-align:center; width:50%; border:1px solid #E2E8F0; padding:4px;">
                        <div style="font-weight:bold; font-size:9px; margin-bottom:2px;">QRIS</div>
                        @if(!empty($qris['image']))
                            <img src="{{ $qris['image'] }}" alt="QRIS" style="width:90px; height:auto;">
                        @endif
                        @if(!empty($qris['merchant']))
                            <div style="color:#475569; font-size:10px; margin-top:4px;">{{ $qris['merchant'] }}</div>
                        @endif
                    </td>
                @endif

                @if($bank && !empty($bank['lines']))
                    <td style="vertical-align:top; {{ $qris ? 'padding-left:5px;' : '' }} border:1px solid #E2E8F0; padding:4px;">
                        <div style="font-weight:bold; font-size:9px; margin-bottom:2px;">TRANSFER REKENING</div>
                        @foreach($bank['lines'] as $line)
                            <div style="font-size:10px; {{ $loop->first ? 'font-weight:bold;' : 'color:#475569;' }}">{{ $line }}</div>
                        @endforeach
                    </td>
                @endif
            </tr>
        </table>

        <div style="margin-top:4px; font-size:9px; color:#B45309; font-weight:bold;">
            TRANSFER HANYA KE REKENING / QRIS DI ATAS. SELAIN ITU DI LUAR TANGGUNG JAWAB KAMI.
        </div>
    @else
        <div style="color:#475569;">{{ $doc['payment_text'] }}</div>
    @endif

    <div style="border-top:1px solid #E2E8F0; margin:5px 0 3px;"></div>
    <div style="text-align:center; color:#475569;">{{ $doc['footer'] }}</div>

</div>