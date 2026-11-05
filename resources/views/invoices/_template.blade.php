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
<div class="invoice-doc" data-layout="{{ count($doc['items'] ?? []) > 12 ? 'compact' : 'normal' }}" style="position:relative; font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #141414; font-size: 14px; line-height:1.35; background: #FFFFFF; width: 100%;">

    {{-- Stempel PAID kalau invoice sudah lunas. --}}
    @if($doc['paid'] ?? false)
        <div style="position:absolute; top:110px; right:40px; transform:rotate(-16deg); border:4px solid #15803D; color:#15803D; padding:6px 18px; font-size:26px; font-weight:bold; letter-spacing:6px; opacity:.85;" aria-label="Sudah dibayar">PAID</div>
    @endif

    {{-- Kop --}}
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top;">
                @if(!empty($doc['logo']))
                    <img src="{{ $doc['logo'] }}" alt="{{ $doc['brand'] }}" style="display:block; max-width:180px; max-height:58px; object-fit:contain; margin-bottom:6px;">
                @endif
                <div style="font-size:22px; font-weight:bold;">{{ $doc['brand'] }}</div>
                @if($doc['owner'])<div style="color:#475569;">{{ $doc['owner'] }}</div>@endif
                @if($doc['store_wa'])<div style="color:#475569;">WA: {{ $doc['store_wa'] }}</div>@endif
            </td>
            <td style="vertical-align:top; text-align:right;">
                <div style="font-size:24px; font-weight:bold;">{{ $doc['title'] }}</div>
                <div style="font-weight:bold; font-size:15px;">#{{ $doc['number'] }}</div>
                <div style="color:#475569; font-size:13px;">{{ $doc['date'] }}</div>
                <div style="margin-top:8px;">
                    <span style="display:inline-block; padding:5px 10px; border:2px solid {{ $doc['status_color'] }}; color:{{ $doc['status_color'] }}; font-weight:bold; font-size:12px;">{{ $doc['status_text'] }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div style="border-top:4px solid #FFC800; margin:12px 0 16px;"></div>

    {{-- Pelanggan & ringkasan --}}
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top; width:55%;">
                <div style="color:#475569; font-size:12px; font-weight:bold;">DITAGIHKAN KEPADA</div>
                <div style="font-weight:bold; font-size:16px; padding-top:6px;">{{ $doc['customer']['name'] }}</div>
                @if($doc['customer']['wa'])<div style="color:#475569;">WA: {{ $doc['customer']['wa'] }}</div>@endif
                @if($doc['customer']['address'])<div style="color:#475569;">{{ $doc['customer']['address'] }}</div>@endif
            </td>
            <td style="vertical-align:top;">
                <div style="color:#475569; font-size:12px; font-weight:bold;">RINGKASAN</div>
                <table style="width:100%; border-collapse:collapse; margin-top:6px;">
                    @foreach($doc['shipping'] as $row)
                        <tr>
                            <td style="color:#475569; padding:4px 0;">{{ $row['label'] }}</td>
                            <td style="text-align:right; font-weight:bold; padding:4px 0;">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- Item --}}
    <table style="width:100%; border-collapse:collapse; margin-top:18px;">
        <tr>
            <td style="background:#FFC800; color:#141414; font-weight:bold; font-size:13px; padding:12px 9px;">Produk</td>
            <td style="background:#FFC800; color:#141414; font-weight:bold; font-size:13px; padding:12px 9px;">Satuan</td>
            <td style="background:#FFC800; color:#141414; font-weight:bold; font-size:13px; padding:12px 9px; text-align:center;">Qty</td>
            <td style="background:#FFC800; color:#141414; font-weight:bold; font-size:13px; padding:12px 9px; text-align:right;">Harga</td>
            @if(!empty($doc['item_has_fee']))
                <td style="background:#FFC800; color:#141414; font-weight:bold; font-size:13px; padding:12px 9px; text-align:right;">Fee item</td>
            @endif
            <td style="background:#FFC800; color:#141414; font-weight:bold; font-size:13px; padding:12px 9px; text-align:right;">Subtotal</td>
        </tr>
        @foreach($doc['items'] as $item)
            <tr>
                <td style="border-bottom:1px solid #CBD5E1; padding:12px 9px;">{{ $item['name'] }}</td>
                <td style="border-bottom:1px solid #CBD5E1; padding:12px 9px; color:#475569;">{{ $item['unit'] }}</td>
                <td style="border-bottom:1px solid #CBD5E1; padding:12px 9px; text-align:center;">{{ $item['qty'] }}</td>
                <td style="border-bottom:1px solid #CBD5E1; padding:12px 9px; text-align:right;">{{ $item['price'] }}</td>
                @if(!empty($doc['item_has_fee']))
                    <td style="border-bottom:1px solid #CBD5E1; padding:12px 9px; text-align:right; color:#92400E;">
                        {{ $item['fee'] ?? '-' }}
                    </td>
                @endif
                <td style="border-bottom:1px solid #CBD5E1; padding:12px 9px; text-align:right; font-weight:bold;">{{ $item['subtotal'] }}</td>
            </tr>
        @endforeach
    </table>

    {{-- Total --}}
    <table style="width:100%; border-collapse:collapse; margin-top:14px;">
        <tr>
            <td style="width:48%;"></td>
            <td class="invoice-totals-box" style="width:52%; border:2px solid #141414; border-radius:0; border-collapse:separate; border-spacing:0; vertical-align:top;">
                <table style="width:100%; border-collapse:collapse;">
                    @foreach($doc['totals'] as $row)
                        <tr>
                            <td style="color:#475569; font-size:13px; padding:7px 10px;">{{ $row['label'] }}</td>
                            <td style="text-align:right; font-size:13px; padding:7px 10px; @if(!empty($row['green'])) color:#166534; font-weight:bold; @elseif(!empty($row['muted'])) color:#475569; @endif">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="background:#FFC800; color:#141414; font-weight:bold; font-size:16px; padding:9px 10px; border-top:2px solid #141414;">
                            {{ $doc['total']['label'] }}
                            @if($doc['left'])
                                <div style="margin-top:5px; font-size:16px;">{{ $doc['left']['label'] }}</div>
                            @endif
                        </td>
                        <td style="background:#FFC800; color:#141414; font-weight:bold; font-size:16px; padding:9px 10px; text-align:right; border-top:2px solid #141414;">
                            {{ $doc['total']['value'] }}
                            @if($doc['left'])
                                <div style="margin-top:4px; font-size:16px;">{{ $doc['left']['value'] }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Pembayaran: QRIS + rekening.
         QRIS hanya muncul kalau diizinkan untuk nominal ini (<= batas);
         di atas batas, hanya rekening. --}}
    <div style="border-top:2px solid #CBD5E1; margin:18px 0 10px;"></div>
    <div style="font-size:12px; font-weight:bold; color:#475569; margin-bottom:8px;">CARA PEMBAYARAN</div>

    @php($qris = $doc['qris'] ?? null)
    @php($bank = $doc['bank'] ?? null)

    @if($doc['paid'] ?? false)
        <div style="font-size:14px; color:#15803D; font-weight:bold; padding:14px 0;">
            Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada '.$doc['paid_at'] : '' }}. Terima kasih.
        </div>
    @elseif($qris || ($bank && !empty($bank['lines'])))
        <table style="width:100%; border-collapse:separate; border-spacing:10px 0; margin-left:-10px;">
            <tr>
                @if($qris)
                    <td style="vertical-align:top; text-align:center; width:42%; border:1px solid #CBD5E1; padding:10px;">
                        <div style="font-weight:bold; font-size:13px; margin-bottom:8px;">
                            <span style="display:inline-block; width:18px; height:18px; vertical-align:-4px; margin-right:5px;">
                                <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true"><rect x="1.5" y="1.5" width="15" height="15" rx="1" fill="none" stroke="#141414" stroke-width="1.5"/><path d="M4 4h3v3H4zm7 0h3v3h-3zM4 11h3v3H4zm7 0h3v3h-3zM9 4v7m0 3v2M9 11H7m2 0h3" fill="none" stroke="#141414" stroke-width="1.2"/></svg>
                            </span>QRIS
                        </div>
                        @if(!empty($qris['image']))
                            <img src="{{ $qris['image'] }}" alt="QRIS pembayaran" style="display:block; width:210px; max-width:100%; height:210px; object-fit:contain; image-rendering:auto; margin:0 auto;">
                        @endif
                        @if(!empty($qris['merchant']))
                            <div style="color:#475569; font-size:12px; margin-top:7px;">{{ $qris['merchant'] }}</div>
                        @endif
                    </td>
                @endif

                @if($bank && !empty($bank['lines']))
                    <td style="vertical-align:top; border:1px solid #CBD5E1; padding:12px;">
                        <div style="font-weight:bold; font-size:13px; margin-bottom:8px;">
                            <span style="display:inline-block; width:18px; height:18px; vertical-align:-4px; margin-right:5px;">
                                <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true"><rect x="1.5" y="4.5" width="15" height="11" rx="1.5" fill="none" stroke="#141414" stroke-width="1.5"/><path d="M1.5 7.5h15M4.5 11.5h4.5M12.5 11.5h1" fill="none" stroke="#141414" stroke-width="1.2"/></svg>
                            </span>TRANSFER REKENING
                        </div>
                        @foreach($bank['lines'] as $line)
                            <div style="font-size:13px; padding:4px 0; {{ $loop->first ? 'font-weight:bold;' : 'color:#475569;' }}">{{ $line }}</div>
                        @endforeach
                    </td>
                @endif
            </tr>
        </table>

        <div style="margin-top:10px; font-size:11px; color:#B45309; font-weight:bold; line-height:1.45;">
            TRANSFER HANYA KE REKENING / QRIS DI ATAS. SELAIN ITU DI LUAR TANGGUNG JAWAB KAMI.
        </div>
    @else
        <div style="color:#475569;">{{ $doc['payment_text'] }}</div>
    @endif

    <div style="border-top:1px solid #E2E8F0; margin:5px 0 3px;"></div>
    <div style="text-align:center; color:#475569;">{{ $doc['footer'] }}</div>

</div>