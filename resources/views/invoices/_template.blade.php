@php
/*
 * ─── INVOICE TEMPLATE v2 ────────────────────────────────────────────────────
 * Shared by: invoices/pdf.blade.php, invoices/html.blade.php,
 *            admin/orders/invoice.blade.php (print preview)
 *
 * All styling is inline — required for DomPDF compatibility.
 * No Tailwind / external CSS allowed here.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── Bank logo base64 data URIs (inline SVG as data URI for DomPDF) ──────────
// BCA – official blue #005FAF
$bcaLogoSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 60"><g><path d="M6,30 L19,6 L34,6 L21,30 L34,54 L19,54 Z" fill="#005FAF"/><path d="M22,30 L35,6 L50,6 L37,30 L50,54 L35,54 Z" fill="#005FAF"/><text x="62" y="43" font-family="Arial Black,Arial,sans-serif" font-size="28" font-weight="900" fill="#005FAF" letter-spacing="1">BCA</text></g></svg>';
$bcaLogo = 'data:image/svg+xml;base64,' . base64_encode($bcaLogoSvg);

// SeaBank – orange #FF5E00, dark #1A1A2E
$seabankLogoSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 60"><path d="M6,44 Q12,20 22,20 Q32,20 32,38 Q32,56 42,56 Q52,56 52,38 Q52,20 62,20 Q72,20 72,38" fill="none" stroke="#FF5E00" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/><text x="82" y="43" font-family="Arial,sans-serif" font-size="24" font-weight="800" fill="#1A1A2E">Sea</text><text x="129" y="43" font-family="Arial,sans-serif" font-size="24" font-weight="800" fill="#FF5E00">Bank</text></svg>';
$seabankLogo = 'data:image/svg+xml;base64,' . base64_encode($seabankLogoSvg);

// BluBCA – dark blue #003A70, cyan #00B2E3
$blubcaLogoSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 60"><circle cx="18" cy="30" r="13" fill="#00B2E3"/><circle cx="18" cy="30" r="7" fill="#FFFFFF"/><circle cx="18" cy="30" r="3" fill="#003A70"/><text x="38" y="42" font-family="Arial Black,Arial,sans-serif" font-size="26" font-weight="900" fill="#003A70">Blu</text><text x="92" y="42" font-family="Arial Black,Arial,sans-serif" font-size="26" font-weight="900" fill="#00B2E3">BCA</text></svg>';
$blubcaLogo = 'data:image/svg+xml;base64,' . base64_encode($blubcaLogoSvg);

$isCompact = count($doc['items'] ?? []) > 12;
$hasFee    = !empty($doc['item_has_fee']);
$qris      = $doc['qris'] ?? null;
$bank      = $doc['bank'] ?? null;
$isPaid    = $doc['paid'] ?? false;
@endphp

<div class="invoice-doc" data-layout="{{ $isCompact ? 'compact' : 'normal' }}"
     style="width:100%; font-family:DejaVu Sans,Arial,Helvetica,sans-serif; color:#141414; font-size:13px; line-height:1.4; background:#FFFFFF; position:relative;">

    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 1 – HEADER
         Left: Logo + Brand name + Owner + WhatsApp
         Right: INVOICE title + Number + Date + Status badge
    ═══════════════════════════════════════════════════════════════════════ --}}
    <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:0;">
        <tr>
            {{-- LEFT: Brand identity --}}
            <td style="width:55%; vertical-align:top; padding-right:16px; padding-bottom:14px;">
                <table role="presentation" style="width:100%; border-collapse:collapse;">
                    <tr>
                        {{-- Logo box with right border divider --}}
                        @if(!empty($doc['logo']))
                        <td style="width:1px; vertical-align:middle; padding-right:14px; border-right:2px solid #E2E8F0; white-space:nowrap;">
                            <img class="invoice-logo"
                                 src="{{ $doc['logo'] }}"
                                 alt="{{ $doc['brand'] }}"
                                 style="display:block; width:68px; height:68px; object-fit:contain; border-radius:8px;">
                        </td>
                        @endif
                        <td style="vertical-align:middle; padding-left:{{ !empty($doc['logo']) ? '14px' : '0' }};">
                            <div style="font-size:22px; font-weight:900; line-height:1.1; color:#141414; letter-spacing:-0.3px;">{{ $doc['brand'] }}</div>
                            @if(!empty($doc['owner']))
                            <div style="font-size:11.5px; color:#64748B; margin-top:3px; font-weight:400;">{{ $doc['owner'] }}</div>
                            @endif
                            @if(!empty($doc['store_wa']))
                            <div style="font-size:11.5px; color:#475569; margin-top:3px; font-weight:400;">
                                <span style="font-size:11px;">📱</span> WhatsApp: {{ $doc['store_wa'] }}
                            </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>

            {{-- RIGHT: INVOICE label + meta --}}
            <td style="width:45%; vertical-align:top; text-align:right; padding-bottom:14px;">
                <div style="font-size:40px; font-weight:900; letter-spacing:1px; line-height:1; color:#141414;">{{ $doc['title'] }}</div>
                <div style="font-size:12px; color:#475569; margin-top:6px; font-weight:500;">Invoice #{{ $doc['number'] }}</div>
                <div style="font-size:12px; color:#475569; margin-top:3px;">
                    <span style="font-size:11px;">📅</span> {{ $doc['date'] }}
                </div>
                <div style="margin-top:8px; text-align:right;">
                    @if($isPaid)
                        <span style="display:inline-block; padding:5px 14px; border:2px solid #15803D; color:#15803D; font-weight:800; font-size:12px; letter-spacing:.5px;">LUNAS</span>
                    @else
                        <span style="display:inline-block; padding:5px 14px; border:2px solid #D97706; color:#D97706; font-weight:800; font-size:12px; letter-spacing:.5px;">BELUM BAYAR</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Yellow divider bar --}}
    <div style="height:5px; background:#FFC800; margin:0 0 16px; border-radius:2px;"></div>


    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 2 – CUSTOMER INFO + ORDER SUMMARY (two info cards)
    ═══════════════════════════════════════════════════════════════════════ --}}
    <table role="presentation" style="width:100%; border-collapse:separate; border-spacing:10px 0; table-layout:fixed; margin-left:-10px; margin-bottom:16px;">
        <tr>
            {{-- LEFT CARD – Ditagihkan Kepada --}}
            <td style="width:50%; vertical-align:top; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:12px 14px;">
                <div style="font-size:10.5px; font-weight:800; color:#64748B; letter-spacing:1px; margin-bottom:8px; display:flex; align-items:center;">
                    <span style="font-size:13px; margin-right:5px;">👤</span> DITAGIHKAN KEPADA
                </div>
                <div style="font-size:10.5px; font-weight:800; color:#64748B; letter-spacing:1px; margin-bottom:8px;">
                </div>
                <div style="font-size:15px; font-weight:800; color:#141414; margin-bottom:5px;">{{ $doc['customer']['name'] }}</div>
                @if(!empty($doc['customer']['wa']))
                <div style="font-size:12px; color:#475569; margin-top:3px;">
                    <span style="font-size:11px;">📱</span> WhatsApp: {{ $doc['customer']['wa'] }}
                </div>
                @endif
                @if(!empty($doc['customer']['address']))
                <div style="font-size:12px; color:#475569; margin-top:3px; line-height:1.5;">
                    <span style="font-size:11px;">📍</span> {{ $doc['customer']['address'] }}
                </div>
                @endif
            </td>

            {{-- RIGHT CARD – Ringkasan --}}
            <td style="width:50%; vertical-align:top; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:12px 14px;">
                <div style="font-size:10.5px; font-weight:800; color:#64748B; letter-spacing:1px; margin-bottom:8px;">
                    <span style="font-size:13px; margin-right:5px;">📦</span> RINGKASAN
                </div>
                <table role="presentation" style="width:100%; border-collapse:collapse;">
                    @foreach($doc['shipping'] as $row)
                    <tr>
                        <td style="font-size:12px; color:#64748B; padding:3px 0; vertical-align:top;">
                            <span style="font-size:11px; margin-right:4px;">{{ $loop->first ? '🚚' : '📋' }}</span>
                            {{ $row['label'] }}
                        </td>
                        <td style="font-size:12px; font-weight:700; text-align:right; padding:3px 0; padding-left:8px; white-space:nowrap;">: {{ $row['value'] }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>


    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 3 – PRODUCT TABLE
    ═══════════════════════════════════════════════════════════════════════ --}}
    <table class="invoice-items" role="table"
           style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:10px;">
        <thead>
            <tr>
                <th style="width:{{ $hasFee ? '36%' : '40%' }}; background:#FFC800; text-align:left; padding:10px 10px; border-bottom:2.5px solid #141414; font-size:12.5px; font-weight:800;">Produk</th>
                <th style="width:10%; background:#FFC800; text-align:center; padding:10px 6px; border-bottom:2.5px solid #141414; font-size:12.5px; font-weight:800;">Satuan</th>
                <th style="width:8%; background:#FFC800; text-align:center; padding:10px 6px; border-bottom:2.5px solid #141414; font-size:12.5px; font-weight:800;">Qty</th>
                <th style="width:{{ $hasFee ? '16%' : '20%' }}; background:#FFC800; text-align:right; padding:10px 8px; border-bottom:2.5px solid #141414; font-size:12.5px; font-weight:800;">Harga</th>
                @if($hasFee)
                <th style="width:13%; background:#FFC800; text-align:right; padding:10px 8px; border-bottom:2.5px solid #141414; font-size:12.5px; font-weight:800;">Fee item</th>
                @endif
                <th style="width:{{ $hasFee ? '17%' : '22%' }}; background:#FFC800; text-align:right; padding:10px 10px; border-bottom:2.5px solid #141414; font-size:12.5px; font-weight:800;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doc['items'] as $i => $item)
            <tr style="{{ $i % 2 === 1 ? 'background:#FAFAFA;' : 'background:#FFFFFF;' }}">
                <td style="border-bottom:1px solid #E2E8F0; padding:9px 10px; vertical-align:top; overflow-wrap:anywhere; font-size:12.5px;">{{ $item['name'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:9px 6px; text-align:center; color:#475569; font-size:12.5px;">{{ $item['unit'] ?: '-' }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:9px 6px; text-align:center; font-size:12.5px;">{{ $item['qty'] }}</td>
                <td style="border-bottom:1px solid #E2E8F0; padding:9px 8px; text-align:right; white-space:nowrap; font-size:12.5px;">{{ $item['price'] }}</td>
                @if($hasFee)
                <td style="border-bottom:1px solid #E2E8F0; padding:9px 8px; text-align:right; color:#92400E; white-space:nowrap; font-size:12.5px;">{{ $item['fee'] ?? '-' }}</td>
                @endif
                <td style="border-bottom:1px solid #E2E8F0; padding:9px 10px; text-align:right; font-weight:700; white-space:nowrap; font-size:12.5px;">{{ $item['subtotal'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 4 – PAYMENT SUMMARY BOX (right-aligned)
    ═══════════════════════════════════════════════════════════════════════ --}}
    <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:18px;">
        <tr>
            <td style="width:42%;"></td>
            <td class="invoice-totals-box"
                style="width:58%; vertical-align:top; border:1.5px solid #CBD5E1; border-radius:8px; overflow:hidden; padding:0;">
                <table role="presentation" style="width:100%; border-collapse:collapse; table-layout:fixed;">
                    {{-- Regular total rows --}}
                    @foreach($doc['totals'] as $row)
                    <tr>
                        <td style="font-size:12.5px; color:#475569; padding:7px 12px; border-bottom:1px solid #F1F5F9; width:55%;">{{ $row['label'] }}</td>
                        <td style="font-size:12.5px; text-align:right; padding:7px 12px; border-bottom:1px solid #F1F5F9; white-space:nowrap; {{ !empty($row['green']) ? 'color:#166534;font-weight:700;' : '' }}">{{ $row['value'] }}</td>
                    </tr>
                    @endforeach

                    {{-- TOTAL BAYAR – yellow background --}}
                    <tr>
                        <td style="background:#FFC800; border-top:2px solid #141414; font-size:14px; font-weight:900; padding:9px 12px; letter-spacing:-.2px;">{{ $doc['total']['label'] }}</td>
                        <td style="background:#FFC800; border-top:2px solid #141414; text-align:right; font-size:14px; font-weight:900; padding:9px 12px; white-space:nowrap;">{{ $doc['total']['value'] }}</td>
                    </tr>

                    {{-- Sisa Bayar – light yellow background --}}
                    @if($doc['left'])
                    <tr>
                        <td style="background:#FEF3C7; border-top:1px solid #FDE68A; font-size:15px; font-weight:800; padding:9px 12px;">{{ $doc['left']['label'] }}</td>
                        <td style="background:#FEF3C7; border-top:1px solid #FDE68A; text-align:right; font-size:15px; font-weight:800; padding:9px 12px; white-space:nowrap;">{{ $doc['left']['value'] }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>


    {{-- ═══════════════════════════════════════════════════════════════════════
         SECTION 5 – CARA PEMBAYARAN
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div style="border-top:1.5px solid #E2E8F0; margin-bottom:12px;"></div>
    <div style="font-size:12px; font-weight:800; color:#141414; letter-spacing:.3px; margin-bottom:10px;">
        <span style="font-size:14px; margin-right:6px;">💳</span> CARA PEMBAYARAN
    </div>

    @if($isPaid)
        {{-- PAID stamp --}}
        <div style="font-size:13px; color:#15803D; font-weight:700; padding:12px 0;">
            ✅ Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada '.$doc['paid_at'] : '' }}. Terima kasih.
        </div>

    @elseif($qris || ($bank && !empty($bank['lines'])))
        {{-- Two large side-by-side payment cards --}}
        <table role="presentation" style="width:100%; border-collapse:separate; border-spacing:10px 0; table-layout:fixed; margin-left:-10px; margin-bottom:10px;">
            <tr>

                {{-- ── LEFT CARD: QRIS ───────────────────────────────────── --}}
                @if($qris)
                <td style="{{ ($bank && !empty($bank['lines'])) ? 'width:48%;' : 'width:100%;' }} vertical-align:top; border:1.5px solid #E2E8F0; border-radius:10px; padding:0; overflow:hidden;">

                    {{-- QRIS card header: yellow bar --}}
                    <div style="background:#FFC800; padding:9px 12px; display:flex; align-items:center;">
                        <span style="font-size:16px; margin-right:7px; vertical-align:middle;">▦</span>
                        <span style="font-size:13px; font-weight:900; color:#141414; vertical-align:middle; letter-spacing:.3px;">PEMBAYARAN QRIS</span>
                    </div>

                    {{-- QR code area --}}
                    <div style="padding:12px 12px 10px; text-align:center; background:#FFFFFF;">
                        @if(!empty($qris['image']))
                        <div style="display:inline-block; background:#FFFFFF; padding:8px; border:1.5px solid #E2E8F0; border-radius:8px;">
                            <img class="qris-invoice-image"
                                 src="{{ $qris['image'] }}"
                                 alt="QRIS JASTIP NITIPDIEND"
                                 style="display:block; width:52mm; height:52mm; max-width:52mm; max-height:52mm; object-fit:contain; image-rendering:-webkit-optimize-contrast; background:#FFFFFF;">
                        </div>
                        @endif

                        <div style="font-size:13px; font-weight:900; color:#141414; margin-top:9px; letter-spacing:.3px;">JASTIP NITIPDIEND</div>
                        <div style="display:inline-block; margin-top:4px; padding:3px 14px; background:#F1F5F9; border-radius:30px; font-size:10.5px; color:#64748B; font-weight:600; letter-spacing:.5px;">SCAN / UPLOAD QRIS</div>
                    </div>
                </td>
                @endif

                {{-- ── RIGHT CARD: TRANSFER REKENING ─────────────────────── --}}
                @if($bank && !empty($bank['lines']))
                <td style="{{ $qris ? 'width:52%;' : 'width:100%;' }} vertical-align:top; border:1.5px solid #E2E8F0; border-radius:10px; padding:0; overflow:hidden;">

                    {{-- Transfer card header --}}
                    <div style="background:#FFFFFF; padding:9px 12px; border-bottom:1px solid #E2E8F0; display:flex; align-items:center;">
                        <span style="font-size:16px; margin-right:7px; vertical-align:middle;">🏦</span>
                        <span style="font-size:13px; font-weight:900; color:#141414; vertical-align:middle; letter-spacing:.3px;">TRANSFER REKENING</span>
                    </div>

                    {{-- Bank account entries --}}
                    <div style="padding:10px 14px; background:#FFFFFF;">
                        @if(!empty($bank['accounts']))
                            @php $accounts = $bank['accounts']; @endphp
                            @foreach($accounts as $idx => $account)
                            <table role="presentation" style="width:100%; border-collapse:collapse; {{ $idx > 0 ? 'border-top:1px solid #F1F5F9; margin-top:8px; padding-top:8px;' : '' }}">
                                <tr>
                                    {{-- Bank logo --}}
                                    <td style="width:52px; vertical-align:middle; padding-right:10px; padding-top:{{ $idx > 0 ? '8px' : '0' }};">
                                        @php
                                            $bankName = strtolower($account['bank'] ?? '');
                                            if (str_contains($bankName, 'blu') || str_contains($bankName, 'blubca')) {
                                                $logoSrc = $blubcaLogo;
                                                $logoH = 28;
                                            } elseif (str_contains($bankName, 'sea')) {
                                                $logoSrc = $seabankLogo;
                                                $logoH = 26;
                                            } else {
                                                $logoSrc = $bcaLogo;
                                                $logoH = 24;
                                            }
                                        @endphp
                                        <img src="{{ $logoSrc }}"
                                             alt="{{ $account['bank'] }}"
                                             style="display:block; width:48px; height:{{ $logoH }}px; object-fit:contain; object-position:left center;">
                                    </td>
                                    {{-- Bank details --}}
                                    <td style="vertical-align:middle; padding-top:{{ $idx > 0 ? '8px' : '0' }};">
                                        <div style="font-size:12.5px; font-weight:800; color:#141414;">{{ $account['bank'] }}</div>
                                        <div style="font-size:11.5px; color:#475569; margin-top:2px; line-height:1.4;">
                                            {{ $account['number'] }} a/n {{ $account['name'] }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endforeach
                        @else
                            @foreach($bank['lines'] as $line)
                            <div style="{{ !$loop->first ? 'border-top:1px solid #F1F5F9; margin-top:7px; padding-top:7px;' : '' }} font-size:12px; color:#475569; line-height:1.4;">{{ $line }}</div>
                            @endforeach
                        @endif
                    </div>
                </td>
                @endif

            </tr>
        </table>

        {{-- ── Warning banner ────────────────────────────────────────────── --}}
        <div style="background:#FFF7ED; border:1.5px solid #FED7AA; border-radius:8px; padding:9px 14px; margin-top:6px; margin-bottom:8px;">
            <table role="presentation" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:22px; vertical-align:middle; padding-right:8px;">
                        <span style="font-size:16px; color:#EA580C;">⚠</span>
                    </td>
                    <td style="vertical-align:middle; font-size:11.5px; font-weight:800; color:#C2410C; line-height:1.45; letter-spacing:.1px;">
                        TRANSFER HANYA KE REKENING / QRIS DI ATAS. SELAIN ITU DI LUAR TANGGUNG JAWAB KAMI.
                    </td>
                </tr>
            </table>
        </div>

    @else
        <div style="font-size:12px; color:#475569; padding:6px 0;">{{ $doc['payment_text'] }}</div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div style="margin-top:10px; padding-top:10px; border-top:1px solid #E2E8F0; text-align:center;">
        <table role="presentation" style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:35%; height:1px; border-top:1px solid #CBD5E1;"></td>
                <td style="white-space:nowrap; padding:0 14px; font-size:11.5px; color:#64748B; text-align:center;">
                    {{ $doc['footer'] }}
                </td>
                <td style="width:35%; height:1px; border-top:1px solid #CBD5E1;"></td>
            </tr>
        </table>
    </div>

</div>
