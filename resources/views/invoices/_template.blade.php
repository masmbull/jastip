@php
/*
 * ─── INVOICE TEMPLATE v2 ────────────────────────────────────────────────────
 * Shared by: invoices/pdf.blade.php (DomPDF), invoices/html.blade.php,
 *            admin/orders/invoice.blade.php (browser print preview)
 *
 * RULES:
 * - All styling must be inline (DomPDF compatibility).
 * - No emoji — DomPDF cannot render them reliably.
 * - No external CSS / Google Fonts in this file.
 * - No flexbox/grid — DomPDF only supports table-based layout.
 * - Compact mode values are computed in PHP and injected as inline styles.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── Bank logos: inline SVG → base64 data URI ─────────────────────────────────
$bcaSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 72">'
    . '<path d="M8,36 L22,8 L36,8 L22,36 L36,64 L22,64 Z" fill="#005FAF"/>'
    . '<path d="M28,36 L42,8 L56,8 L42,36 L56,64 L42,64 Z" fill="#005FAF"/>'
    . '<text x="68" y="50" font-family="Arial Black,Arial,sans-serif" font-size="36" font-weight="900" fill="#005FAF" letter-spacing="1">BCA</text>'
    . '</svg>';
$bcaLogo = 'data:image/svg+xml;base64,' . base64_encode($bcaSvg);

$seaSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 72">'
    . '<path d="M8,26 Q14,8 26,8 Q38,8 38,28 Q38,48 50,48 Q62,48 62,28 Q62,8 74,8 Q82,8 86,16" fill="none" stroke="#FF5722" stroke-width="8" stroke-linecap="round"/>'
    . '<path d="M8,48 Q14,30 26,30 Q38,30 38,50 Q38,64 50,64 Q62,64 62,50 Q62,30 74,30 Q82,30 86,38" fill="none" stroke="#FF5722" stroke-width="6" stroke-linecap="round" opacity="0.55"/>'
    . '<text x="100" y="51" font-family="Arial,Helvetica,sans-serif" font-size="30" font-weight="800" fill="#1B1F2E">Sea</text>'
    . '<text x="168" y="51" font-family="Arial,Helvetica,sans-serif" font-size="30" font-weight="800" fill="#FF5722">Bank</text>'
    . '</svg>';
$seaLogo = 'data:image/svg+xml;base64,' . base64_encode($seaSvg);

$bluSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 72">'
    . '<circle cx="24" cy="36" r="22" fill="#00B8D4"/>'
    . '<circle cx="24" cy="36" r="12" fill="#FFFFFF"/>'
    . '<circle cx="24" cy="36" r="5" fill="#003B5C"/>'
    . '<text x="56" y="51" font-family="Arial Black,Arial,sans-serif" font-size="32" font-weight="900" fill="#003B5C">Blu</text>'
    . '<text x="132" y="51" font-family="Arial Black,Arial,sans-serif" font-size="32" font-weight="900" fill="#00B8D4">BCA</text>'
    . '</svg>';
$bluLogo = 'data:image/svg+xml;base64,' . base64_encode($bluSvg);

// ── Layout mode — compact = 13+ items ────────────────────────────────────────
$isCompact = count($doc['items'] ?? []) > 12;
$hasFee    = !empty($doc['item_has_fee']);
$qris      = $doc['qris'] ?? null;
$bank      = $doc['bank'] ?? null;
$isPaid    = $doc['paid'] ?? false;

// ── PHP-driven size tokens — injected directly as inline styles ───────────────
// This is the only reliable way to shrink ALL sections in DomPDF.
$fs         = $isCompact ? '9px'   : '13px';   // base font size
$fsSmall    = $isCompact ? '8px'   : '11px';   // secondary text
$fsMed      = $isCompact ? '9px'   : '12px';   // medium text
$fsLg       = $isCompact ? '10px'  : '15px';   // large text (customer name)
$fsXl       = $isCompact ? '18px'  : '42px';   // INVOICE title
$fsTitle    = $isCompact ? '10px'  : '22px';   // brand name
$lh         = $isCompact ? '1.05'  : '1.45';   // line height
$padCard    = $isCompact ? '5px 7px' : '11px 13px'; // card padding
$padTh      = $isCompact ? '4px 5px' : '9px 10px';  // table header padding
$padTd      = $isCompact ? '3px 5px' : '8px 10px';  // table cell padding
$padBox     = $isCompact ? '4px 8px' : '6px 12px';  // totals box padding
$padBoxLg   = $isCompact ? '5px 8px' : '9px 12px';  // totals TOTAL BAYAR row
$padBoxSisa = $isCompact ? '5px 8px' : '9px 12px';  // totals Sisa row
$fsTotalLg  = $isCompact ? '10px'  : '13.5px'; // TOTAL BAYAR font
$fsSisa     = $isCompact ? '10px'  : '15px';   // Sisa Bayar font
$logoSz     = $isCompact ? '44px'  : '72px';   // logo size
$marginSec  = $isCompact ? '8px'   : '16px';   // section bottom margin
$marginBar  = $isCompact ? '4px 0 8px' : '2px 0 16px'; // yellow bar margin
$qrisMm     = $isCompact ? '38mm'  : '60mm';   // QRIS image size
$bankLogoW  = $isCompact ? '48px'  : '64px';   // BCA logo width
$bankLogoWS = $isCompact ? '54px'  : '80px';   // SeaBank logo width
$bankLogoWB = $isCompact ? '52px'  : '72px';   // BluBCA logo width
$bankLogoH  = $isCompact ? '20px'  : '28px';   // BCA logo height
$bankLogoHS = $isCompact ? '20px'  : '32px';   // SeaBank logo height
$bankLogoPad = $isCompact ? '5px 8px' : '12px 14px'; // bank card padding
$bankRowMT  = $isCompact ? '6px'   : '10px';   // bank row margin-top
$fsBank     = $isCompact ? '9px'   : '13px';   // bank name font
$fsBankSub  = $isCompact ? '8px'   : '11.5px'; // bank account font
$padQrisInner = $isCompact ? '6px' : '12px';   // QRIS inner padding
$padPayHdr  = $isCompact ? '4px 8px' : '8px 12px'; // payment card header padding
@endphp

<div class="invoice-doc" data-layout="{{ $isCompact ? 'compact' : 'normal' }}"
     style="width:100%; font-family:DejaVu Sans,Arial,Helvetica,sans-serif; color:#141414; font-size:{{ $fs }}; line-height:{{ $lh }}; background:#FFFFFF;">

    {{-- ══════════════════════════════════════════════════════════════
         1. HEADER
    ══════════════════════════════════════════════════════════════ --}}
    <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:0;">
        <tr>
            {{-- LEFT – brand identity --}}
            <td style="width:54%; vertical-align:top; padding-right:12px; padding-bottom:{{ $isCompact ? '6px' : '12px' }};">
                <table role="presentation" style="width:100%; border-collapse:collapse;">
                    <tr>
                        @if(!empty($doc['logo']))
                        <td style="width:{{ $logoSz }}; vertical-align:middle; padding-right:10px; border-right:2px solid #E2E8F0;">
                            <img class="invoice-logo"
                                 src="{{ $doc['logo'] }}"
                                 alt="{{ $doc['brand'] }}"
                                 style="display:block; width:{{ $logoSz }}; height:{{ $logoSz }}; object-fit:contain; border-radius:4px;">
                        </td>
                        @endif
                        <td style="vertical-align:middle; padding-left:{{ !empty($doc['logo']) ? '10px' : '0' }};">
                            <div style="font-size:{{ $fsTitle }}; font-weight:900; line-height:1.1; color:#141414; letter-spacing:-0.5px;">{{ $doc['brand'] }}</div>
                            @if(!empty($doc['owner']))
                            <div style="font-size:{{ $fsSmall }}; color:#64748B; margin-top:{{ $isCompact ? '2px' : '4px' }};">{{ $doc['owner'] }}</div>
                            @endif
                            @if(!empty($doc['store_wa']))
                            <div style="font-size:{{ $fsSmall }}; color:#475569; margin-top:2px;">WhatsApp: {{ $doc['store_wa'] }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>

            {{-- RIGHT – invoice meta --}}
            <td style="width:46%; vertical-align:top; text-align:right; padding-bottom:{{ $isCompact ? '6px' : '12px' }};">
                <div style="font-size:{{ $fsXl }}; font-weight:900; letter-spacing:2px; line-height:1; color:#141414;">{{ $doc['title'] }}</div>
                <div style="font-size:{{ $fsMed }}; color:#475569; margin-top:{{ $isCompact ? '3px' : '6px' }};">Invoice #{{ $doc['number'] }}</div>
                <div style="font-size:{{ $fsSmall }}; color:#475569; margin-top:2px;">{{ $doc['date'] }}</div>
                <table role="presentation" style="width:100%; border-collapse:collapse; margin-top:{{ $isCompact ? '4px' : '8px' }};">
                    <tr>
                        <td style="text-align:right;">
                            @if($isPaid)
                                <span style="display:inline-block; padding:3px 12px; border:2px solid #15803D; border-radius:4px; color:#15803D; font-weight:800; font-size:{{ $isCompact ? '8px' : '11px' }}; letter-spacing:1px; background:#F0FDF4;">LUNAS</span>
                            @else
                                <span style="display:inline-block; padding:3px 12px; border:2px solid #D97706; border-radius:4px; color:#D97706; font-weight:800; font-size:{{ $isCompact ? '8px' : '11px' }}; letter-spacing:.5px; background:#FFFBEB;">BELUM BAYAR</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Yellow accent bar --}}
    <div style="height:{{ $isCompact ? '3px' : '5px' }}; background:#FFC800; margin:{{ $marginBar }}; border-radius:2px;"></div>


    {{-- ══════════════════════════════════════════════════════════════
         2. CUSTOMER INFO + ORDER SUMMARY
    ══════════════════════════════════════════════════════════════ --}}
    <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:{{ $marginSec }};">
        <tr>
            {{-- LEFT CARD --}}
            <td style="width:49%; vertical-align:top; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:{{ $padCard }};">
                <div style="font-size:{{ $isCompact ? '7.5px' : '10px' }}; font-weight:800; color:#64748B; letter-spacing:1px; margin-bottom:{{ $isCompact ? '4px' : '7px' }};">&#x25C9; DITAGIHKAN KEPADA</div>
                <div style="font-size:{{ $fsLg }}; font-weight:800; color:#141414; margin-bottom:3px;">{{ $doc['customer']['name'] }}</div>
                @if(!empty($doc['customer']['wa']))
                <div style="font-size:{{ $fsSmall }}; color:#475569; margin-top:2px;">WhatsApp: {{ $doc['customer']['wa'] }}</div>
                @endif
                @if(!empty($doc['customer']['address']))
                <div style="font-size:{{ $fsSmall }}; color:#475569; margin-top:2px; line-height:1.4;">{{ $doc['customer']['address'] }}</div>
                @endif
            </td>

            <td style="width:2%;"></td>

            {{-- RIGHT CARD --}}
            <td style="width:49%; vertical-align:top; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:{{ $padCard }};">
                <div style="font-size:{{ $isCompact ? '7.5px' : '10px' }}; font-weight:800; color:#64748B; letter-spacing:1px; margin-bottom:{{ $isCompact ? '4px' : '7px' }};">&#x25A6; RINGKASAN</div>
                <table role="presentation" style="width:100%; border-collapse:collapse;">
                    @foreach($doc['shipping'] as $row)
                    <tr>
                        <td style="font-size:{{ $fsSmall }}; color:#64748B; padding:2px 0; vertical-align:top; width:45%;">{{ $row['label'] }}</td>
                        <td style="font-size:{{ $fsSmall }}; font-weight:700; text-align:left; padding:2px 0 2px 4px;">: {{ $row['value'] }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>


    {{-- ══════════════════════════════════════════════════════════════
         3. PRODUCT TABLE
    ══════════════════════════════════════════════════════════════ --}}
    <table class="invoice-items" role="table"
           style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:{{ $isCompact ? '6px' : '10px' }};">
        <thead>
            <tr>
                <th style="width:{{ $hasFee ? '35%' : '40%' }}; background:#FFC800; text-align:left; padding:{{ $padTh }}; border-bottom:2px solid #141414; font-size:{{ $fsMed }}; font-weight:900;">Produk</th>
                <th style="width:10%; background:#FFC800; text-align:center; padding:{{ $isCompact ? '4px 3px' : '9px 5px' }}; border-bottom:2px solid #141414; font-size:{{ $fsMed }}; font-weight:900;">Satuan</th>
                <th style="width:8%; background:#FFC800; text-align:center; padding:{{ $isCompact ? '4px 3px' : '9px 5px' }}; border-bottom:2px solid #141414; font-size:{{ $fsMed }}; font-weight:900;">Qty</th>
                <th style="width:{{ $hasFee ? '16%' : '20%' }}; background:#FFC800; text-align:right; padding:{{ $isCompact ? '4px 5px' : '9px 8px' }}; border-bottom:2px solid #141414; font-size:{{ $fsMed }}; font-weight:900;">Harga</th>
                @if($hasFee)
                <th style="width:13%; background:#FFC800; text-align:right; padding:{{ $isCompact ? '4px 5px' : '9px 7px' }}; border-bottom:2px solid #141414; font-size:{{ $fsMed }}; font-weight:900;">Fee item</th>
                @endif
                <th style="width:{{ $hasFee ? '18%' : '22%' }}; background:#FFC800; text-align:right; padding:{{ $padTh }}; border-bottom:2px solid #141414; font-size:{{ $fsMed }}; font-weight:900;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doc['items'] as $item)
            <tr>
                <td style="border-bottom:1px solid #E2E8F0; padding:{{ $padTd }}; vertical-align:top; overflow-wrap:anywhere; font-size:{{ $fsMed }};">{{ $item['name'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:{{ $isCompact ? '3px' : '8px 5px' }}; text-align:center; color:#475569; font-size:{{ $fsMed }};">{{ $item['unit'] ?: '-' }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:{{ $isCompact ? '3px' : '8px 5px' }}; text-align:center; font-size:{{ $fsMed }};">{{ $item['qty'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:{{ $isCompact ? '3px 5px' : '8px' }}; text-align:right; white-space:nowrap; font-size:{{ $fsMed }};">{{ $item['price'] }}</td>
                @if($hasFee)
                <td style="border-bottom:1px solid #E2E8F0; padding:{{ $isCompact ? '3px 5px' : '8px 7px' }}; text-align:right; color:#92400E; white-space:nowrap; font-size:{{ $fsMed }};">{{ $item['fee'] ?? '-' }}</td>
                @endif
                <td style="border-bottom:1px solid #E2E8F0; padding:{{ $padTd }}; text-align:right; font-weight:700; white-space:nowrap; font-size:{{ $fsMed }};">{{ $item['subtotal'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    {{-- ══════════════════════════════════════════════════════════════
         4. PAYMENT SUMMARY BOX (right-aligned 58%)
    ══════════════════════════════════════════════════════════════ --}}
    <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:{{ $marginSec }};">
        <tr>
            <td style="width:42%;"></td>
            <td class="invoice-totals-box"
                style="width:58%; vertical-align:top; border:1.5px solid #CBD5E1; border-radius:6px; overflow:hidden; padding:0;">
                <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed;">
                    @foreach($doc['totals'] as $row)
                    <tr>
                        <td style="font-size:{{ $fsMed }}; color:#475569; padding:{{ $padBox }}; border-bottom:1px solid #F1F5F9; width:54%;">{{ $row['label'] }}</td>
                        <td style="font-size:{{ $fsMed }}; text-align:right; padding:{{ $padBox }}; border-bottom:1px solid #F1F5F9; white-space:nowrap; {{ !empty($row['green']) ? 'color:#166534;font-weight:700;' : '' }}">{{ $row['value'] }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td style="background:#FFC800; border-top:2px solid #141414; font-size:{{ $fsTotalLg }}; font-weight:900; padding:{{ $padBoxLg }};">{{ $doc['total']['label'] }}</td>
                        <td style="background:#FFC800; border-top:2px solid #141414; text-align:right; font-size:{{ $fsTotalLg }}; font-weight:900; padding:{{ $padBoxLg }}; white-space:nowrap;">{{ $doc['total']['value'] }}</td>
                    </tr>
                    @if($doc['left'])
                    <tr>
                        <td style="background:#FEF3C7; border-top:1px solid #FDE68A; font-size:{{ $fsSisa }}; font-weight:800; padding:{{ $padBoxSisa }};">{{ $doc['left']['label'] }}</td>
                        <td style="background:#FEF3C7; border-top:1px solid #FDE68A; text-align:right; font-size:{{ $fsSisa }}; font-weight:800; padding:{{ $padBoxSisa }}; white-space:nowrap;">{{ $doc['left']['value'] }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>


    {{-- ══════════════════════════════════════════════════════════════
         5. CARA PEMBAYARAN
    ══════════════════════════════════════════════════════════════ --}}
    <div style="height:1.5px; background:#E2E8F0; margin-bottom:{{ $isCompact ? '6px' : '10px' }};"></div>
    <div style="font-size:{{ $fsMed }}; font-weight:800; color:#141414; letter-spacing:.3px; margin-bottom:{{ $isCompact ? '6px' : '10px' }};">&#x25A0; CARA PEMBAYARAN</div>

    @if($isPaid)
        <div style="font-size:{{ $fsMed }}; color:#15803D; font-weight:700; padding:{{ $isCompact ? '5px 0' : '10px 0' }};">
            Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada '.$doc['paid_at'] : '' }}. Terima kasih.
        </div>

    @elseif($qris || ($bank && !empty($bank['lines'])))
        <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:{{ $isCompact ? '6px' : '10px' }};">
            <tr>
                {{-- ── LEFT: QRIS ───────────────────────────────── --}}
                @if($qris)
                <td style="{{ ($bank && !empty($bank['lines'])) ? 'width:47%;' : 'width:100%;' }} vertical-align:top; border:1.5px solid #E2E8F0; border-radius:8px; overflow:hidden; padding:0;">
                    <div style="background:#FFC800; padding:{{ $padPayHdr }};">
                        <span style="font-size:{{ $fsMed }}; font-weight:900; color:#141414; letter-spacing:.3px;">&#x25A6; PEMBAYARAN QRIS</span>
                    </div>
                    <div style="padding:{{ $padQrisInner }}; text-align:center; background:#FFFFFF;">
                        @if(!empty($qris['image']))
                        <div style="display:inline-block; background:#FFFFFF; padding:4px; border:1.5px solid #E2E8F0; border-radius:4px;">
                            <img class="qris-invoice-image"
                                 src="{{ $qris['image'] }}"
                                 alt="QRIS JASTIP NITIPDIEND"
                                 style="display:block; width:60mm; height:60mm; max-width:60mm; max-height:60mm; object-fit:contain; image-rendering:-webkit-optimize-contrast; background:#FFFFFF;">
                        </div>
                        @endif
                        <div style="font-size:{{ $fsMed }}; font-weight:900; color:#141414; margin-top:{{ $isCompact ? '4px' : '8px' }}; letter-spacing:.3px;">JASTIP NITIPDIEND</div>
                        <div style="display:inline-block; margin-top:3px; padding:2px 10px; background:#F1F5F9; border-radius:20px; font-size:{{ $isCompact ? '7px' : '10px' }}; color:#64748B; font-weight:600; letter-spacing:.5px;">SCAN / UPLOAD QRIS</div>
                    </div>
                </td>
                @endif

                @if($qris && $bank && !empty($bank['lines']))
                <td style="width:6%; padding:0;"></td>
                @endif

                {{-- ── RIGHT: TRANSFER REKENING ─────────────────── --}}
                @if($bank && !empty($bank['lines']))
                <td style="{{ $qris ? 'width:47%;' : 'width:100%;' }} vertical-align:top; border:1.5px solid #E2E8F0; border-radius:8px; overflow:hidden; padding:0;">
                    <div style="background:#FFFFFF; padding:{{ $padPayHdr }}; border-bottom:1.5px solid #E2E8F0;">
                        <span style="font-size:{{ $fsMed }}; font-weight:900; color:#141414; letter-spacing:.3px;">&#x25A3; TRANSFER REKENING</span>
                    </div>
                    <div style="padding:{{ $bankLogoPad }}; background:#FFFFFF;">
                        @if(!empty($bank['accounts']))
                            @foreach($bank['accounts'] as $idx => $account)
                            @php
                                $bankKey = strtolower($account['bank'] ?? '');
                                if (str_contains($bankKey, 'blu')) {
                                    $logo  = $bluLogo;
                                    $lw    = $bankLogoWB;
                                    $lh    = $bankLogoHS;
                                } elseif (str_contains($bankKey, 'sea')) {
                                    $logo  = $seaLogo;
                                    $lw    = $bankLogoWS;
                                    $lh    = $bankLogoHS;
                                } else {
                                    $logo  = $bcaLogo;
                                    $lw    = $bankLogoW;
                                    $lh    = $bankLogoH;
                                }
                            @endphp
                            <table role="presentation"
                                   style="width:100%; border-collapse:collapse; {{ $idx > 0 ? 'border-top:1px solid #F1F5F9; padding-top:'.$bankRowMT.'; margin-top:'.$bankRowMT.';' : '' }}">
                                <tr>
                                    <td style="width:{{ $lw }}; vertical-align:middle; padding-right:8px; {{ $idx > 0 ? 'padding-top:'.$bankRowMT.';' : '' }}">
                                        <img src="{{ $logo }}"
                                             alt="{{ $account['bank'] }}"
                                             style="display:block; width:{{ $lw }}; height:{{ $lh }}; object-fit:contain; object-position:left center;">
                                    </td>
                                    <td style="vertical-align:middle; {{ $idx > 0 ? 'padding-top:'.$bankRowMT.';' : '' }}">
                                        <div style="font-size:{{ $fsBank }}; font-weight:800; color:#141414; line-height:1.3;">{{ $account['bank'] }}</div>
                                        <div style="font-size:{{ $fsBankSub }}; color:#475569; margin-top:1px; line-height:1.35;">{{ $account['number'] }} a/n {{ $account['name'] }}</div>
                                    </td>
                                </tr>
                            </table>
                            @endforeach
                        @else
                            @foreach($bank['lines'] as $line)
                            <div style="{{ !$loop->first ? 'border-top:1px solid #F1F5F9; margin-top:6px; padding-top:6px;' : '' }} font-size:{{ $fsMed }}; color:#475569; line-height:1.4;">{{ $line }}</div>
                            @endforeach
                        @endif
                    </div>
                </td>
                @endif
            </tr>
        </table>

        {{-- Warning banner --}}
        <div style="background:#FFF7ED; border:1.5px solid #FDBA74; border-radius:6px; padding:{{ $isCompact ? '5px 8px' : '8px 12px' }}; margin-top:3px; margin-bottom:{{ $isCompact ? '4px' : '6px' }};">
            <table role="presentation" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:16px; vertical-align:middle; padding-right:6px; font-size:{{ $isCompact ? '10px' : '14px' }}; color:#EA580C; font-weight:900;">!</td>
                    <td style="vertical-align:middle; font-size:{{ $isCompact ? '8px' : '11px' }}; font-weight:800; color:#C2410C; line-height:1.4; letter-spacing:.1px;">
                        TRANSFER HANYA KE REKENING / QRIS DI ATAS. SELAIN ITU DI LUAR TANGGUNG JAWAB KAMI.
                    </td>
                </tr>
            </table>
        </div>

    @else
        <div style="font-size:{{ $fsMed }}; color:#475569; padding:{{ $isCompact ? '3px 0' : '6px 0' }};">{{ $doc['payment_text'] }}</div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════
         6. FOOTER
    ══════════════════════════════════════════════════════════════ --}}
    <div style="height:1px; background:#E2E8F0; margin-top:{{ $isCompact ? '8px' : '14px' }}; margin-bottom:{{ $isCompact ? '4px' : '8px' }};"></div>
    <table role="presentation" style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:30%; border-bottom:1px solid #CBD5E1; vertical-align:middle; height:1px;"></td>
            <td style="text-align:center; padding:3px 10px; white-space:nowrap; font-size:{{ $isCompact ? '8px' : '11.5px' }}; color:#64748B;">
                {{ $doc['footer'] }}
            </td>
            <td style="width:30%; border-bottom:1px solid #CBD5E1; vertical-align:middle; height:1px;"></td>
        </tr>
    </table>

</div>
