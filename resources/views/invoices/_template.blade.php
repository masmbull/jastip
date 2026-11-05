<div class="invoice-doc" data-layout="{{ count($doc['items'] ?? []) > 12 ? 'compact' : 'normal' }}"
     style="width:100%; font-family:DejaVu Sans,Arial,sans-serif; color:#141414; font-size:13px; line-height:1.35; background:#FFFFFF;">

    @if($doc['paid'] ?? false)
        <div style="position:absolute; top:108px; right:32px; transform:rotate(-14deg); border:3px solid #15803D; color:#15803D; padding:4px 14px; font-size:22px; font-weight:bold; letter-spacing:5px; opacity:.8;">PAID</div>
    @endif

    <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed;">
        <tr>
            <td style="width:58%; vertical-align:top; padding-right:18px;">
                @if(!empty($doc['logo']))
                    <img class="invoice-logo" src="{{ $doc['logo'] }}" alt="{{ $doc['brand'] }}" style="display:block; max-width:150px; max-height:52px; object-fit:contain; margin:0 0 7px;">
                @endif
                <div style="font-size:21px; font-weight:bold; line-height:1.15;">{{ $doc['brand'] }}</div>
                @if(!empty($doc['owner']))<div style="font-size:12px; color:#475569; margin-top:4px;">{{ $doc['owner'] }}</div>@endif
                @if(!empty($doc['store_wa']))<div style="font-size:12px; color:#475569; margin-top:2px;">WhatsApp: {{ $doc['store_wa'] }}</div>@endif
            </td>
            <td style="width:42%; vertical-align:top; text-align:right;">
                <div style="font-size:28px; font-weight:bold; letter-spacing:.5px; line-height:1.1;">{{ $doc['title'] }}</div>
                <div style="font-size:13px; font-weight:bold; margin-top:7px;">Invoice #{{ $doc['number'] }}</div>
                <div style="font-size:12px; color:#475569; margin-top:3px;">{{ $doc['date'] }}</div>
                <div style="margin-top:9px;">
                    <span style="display:inline-block; min-width:118px; text-align:center; padding:6px 10px; border:1px solid {{ $doc['status_color'] }}; color:{{ $doc['status_color'] }}; font-weight:bold; font-size:12px;">{{ $doc['status_text'] }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div style="height:4px; background:#FFC800; margin:14px 0 15px;"></div>

    <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed;">
        <tr>
            <td style="width:58%; vertical-align:top; padding-right:24px;">
                <div style="font-size:11px; font-weight:bold; color:#64748B; letter-spacing:.8px;">DITAGIHKAN KEPADA</div>
                <div style="font-size:15px; font-weight:bold; margin-top:7px;">{{ $doc['customer']['name'] }}</div>
                @if(!empty($doc['customer']['wa']))<div style="font-size:12px; color:#475569; margin-top:3px;">WhatsApp: {{ $doc['customer']['wa'] }}</div>@endif
                @if(!empty($doc['customer']['address']))<div style="font-size:12px; color:#475569; margin-top:3px; line-height:1.45;">{{ $doc['customer']['address'] }}</div>@endif
            </td>
            <td style="width:42%; vertical-align:top;">
                <div style="font-size:11px; font-weight:bold; color:#64748B; letter-spacing:.8px;">RINGKASAN</div>
                <table role="presentation" style="width:100%; border-collapse:collapse; margin-top:5px;">
                    @foreach($doc['shipping'] as $row)
                        <tr>
                            <td style="font-size:12px; color:#64748B; padding:3px 0; width:38%; vertical-align:top;">{{ $row['label'] }}</td>
                            <td style="font-size:12px; font-weight:bold; text-align:right; padding:3px 0 3px 10px;">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    @php($hasFee = !empty($doc['item_has_fee']))
    <table class="invoice-items" role="table" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:16px;">
        <thead>
            <tr>
                <th style="width:38%; background:#FFC800; text-align:left; padding:9px 8px; border-bottom:2px solid #141414; font-size:12px;">Produk</th>
                <th style="width:10%; background:#FFC800; text-align:center; padding:9px 5px; border-bottom:2px solid #141414; font-size:12px;">Satuan</th>
                <th style="width:9%; background:#FFC800; text-align:center; padding:9px 5px; border-bottom:2px solid #141414; font-size:12px;">Qty</th>
                <th style="width:{{ $hasFee ? '17%' : '21%' }}; background:#FFC800; text-align:right; padding:9px 7px; border-bottom:2px solid #141414; font-size:12px;">Harga</th>
                @if($hasFee)
                    <th style="width:13%; background:#FFC800; text-align:right; padding:9px 7px; border-bottom:2px solid #141414; font-size:12px;">Fee item</th>
                @endif
                <th style="width:{{ $hasFee ? '13%' : '22%' }}; background:#FFC800; text-align:right; padding:9px 8px; border-bottom:2px solid #141414; font-size:12px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doc['items'] as $item)
                <tr>
                    <td style="border-bottom:1px solid #E2E8F0; padding:8px; vertical-align:top; overflow-wrap:anywhere;">{{ $item['name'] }}</td>
                    <td style="border-bottom:1px solid #E2E8F0; padding:8px 5px; text-align:center; color:#475569;">{{ $item['unit'] ?: '-' }}</td>
                    <td style="border-bottom:1px solid #E2E8F0; padding:8px 5px; text-align:center;">{{ $item['qty'] }}</td>
                    <td style="border-bottom:1px solid #E2E8F0; padding:8px 7px; text-align:right; white-space:nowrap;">{{ $item['price'] }}</td>
                    @if($hasFee)
                        <td style="border-bottom:1px solid #E2E8F0; padding:8px 7px; text-align:right; color:#92400E; white-space:nowrap;">{{ $item['fee'] ?? '-' }}</td>
                    @endif
                    <td style="border-bottom:1px solid #E2E8F0; padding:8px; text-align:right; font-weight:bold; white-space:nowrap;">{{ $item['subtotal'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:12px;">
        <tr>
            <td style="width:45%;"></td>
            <td class="invoice-totals-box" style="width:55%; vertical-align:top; border:1px solid #94A3B8;">
                <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed;">
                    @foreach($doc['totals'] as $row)
                        <tr>
                            <td style="width:55%; color:#475569; font-size:12px; padding:5px 9px;">{{ $row['label'] }}</td>
                            <td style="width:45%; text-align:right; font-size:12px; padding:5px 9px; white-space:nowrap; {{ !empty($row['green']) ? 'color:#166534;font-weight:bold;' : '' }}">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="background:#FFC800; border-top:1px solid #141414; font-size:13px; font-weight:bold; padding:8px 9px;">{{ $doc['total']['label'] }}</td>
                        <td style="background:#FFC800; border-top:1px solid #141414; text-align:right; font-size:13px; font-weight:bold; padding:8px 9px; white-space:nowrap;">{{ $doc['total']['value'] }}</td>
                    </tr>
                    @if($doc['left'])
                        <tr>
                            <td style="background:#FDE68A; border-top:1px solid #141414; font-size:15px; font-weight:bold; padding:9px;">{{ $doc['left']['label'] }}</td>
                            <td style="background:#FDE68A; border-top:1px solid #141414; text-align:right; font-size:15px; font-weight:bold; padding:9px; white-space:nowrap;">{{ $doc['left']['value'] }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div style="border-top:1px solid #CBD5E1; margin:14px 0 8px;"></div>
    <div style="font-size:11px; font-weight:bold; color:#64748B; letter-spacing:.8px; margin-bottom:7px;">CARA PEMBAYARAN</div>

    @php($qris = $doc['qris'] ?? null)
    @php($bank = $doc['bank'] ?? null)

    @if($doc['paid'] ?? false)
        <div style="font-size:13px; color:#15803D; font-weight:bold; padding:10px 0;">
            Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada '.$doc['paid_at'] : '' }}. Terima kasih.
        </div>
    @elseif($qris || ($bank && !empty($bank['lines'])))
        <table role="presentation" style="width:100%; border-collapse:separate; border-spacing:7px 0; table-layout:fixed; margin-left:-7px;">
            <tr>
                @if($qris)
                    <td style="{{ ($bank && !empty($bank['lines'])) ? 'width:58%;' : 'width:100%;' }} vertical-align:top; text-align:center; border:1px solid #CBD5E1; padding:9px 8px 7px;">
                        <div style="font-weight:bold; font-size:13px; margin-bottom:7px;">PEMBAYARAN QRIS</div>
                        @if(!empty($qris['image']))
                            <img class="qris-invoice-image" src="{{ $qris['image'] }}" alt="QRIS pembayaran" style="display:block; width:60mm; height:60mm; max-width:60mm; max-height:60mm; object-fit:contain; image-rendering:-webkit-optimize-contrast; background:#FFFFFF; margin:0 auto;">
                        @endif
                        <div style="font-size:12px; font-weight:bold; color:#141414; margin-top:5px;">JASTIP NITIPDIEND</div>
                        <div style="font-size:10px; color:#475569; margin-top:2px;">SCAN / UPLOAD QRIS</div>
                    </td>
                @endif

                @if($bank && !empty($bank['lines']))
                    <td style="{{ $qris ? 'width:42%;' : 'width:100%;' }} vertical-align:top; border:1px solid #CBD5E1; padding:9px 10px;">
                        <div style="font-weight:bold; font-size:13px; margin-bottom:6px;">TRANSFER REKENING</div>
                        @if(!empty($bank['accounts']))
                            @foreach($bank['accounts'] as $account)
                                <div style="{{ !$loop->first ? 'border-top:1px solid #E2E8F0; margin-top:6px; padding-top:6px;' : '' }}">
                                    <div style="font-size:12px; font-weight:bold; margin-bottom:2px;">{{ $account['bank'] }}</div>
                                    <div style="font-size:12px; font-weight:normal; line-height:1.35;">{{ $account['number'] }} a/n {{ $account['name'] }}</div>
                                </div>
                            @endforeach
                        @else
                            @foreach($bank['lines'] as $line)
                                <div style="{{ !$loop->first ? 'border-top:1px solid #E2E8F0; margin-top:6px; padding-top:6px;' : '' }} font-size:12px; line-height:1.35;">{{ $line }}</div>
                            @endforeach
                        @endif
                    </td>
                @endif
            </tr>
        </table>
        <div style="margin-top:7px; font-size:10px; color:#92400E; font-weight:bold; line-height:1.35;">
            TRANSFER HANYA KE REKENING / QRIS DI ATAS. SELAIN ITU DI LUAR TANGGUNG JAWAB KAMI.
        </div>
    @else
        <div style="font-size:12px; color:#475569; padding:5px 0;">{{ $doc['payment_text'] }}</div>
    @endif

    <div style="border-top:1px solid #E2E8F0; margin:8px 0 5px;"></div>
    <div style="text-align:center; font-size:11px; color:#475569;">{{ $doc['footer'] }}</div>
</div>
