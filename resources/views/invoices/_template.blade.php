@php
/*
 * ─── INVOICE TEMPLATE v3 ────────────────────────────────────────────────────
 * Renderer: Spatie Browsershot (Chrome headless) — primary
 *           DomPDF — fallback only
 *
 * Since Chrome is the renderer, we can use:
 *  ✓ Flexbox / CSS Grid
 *  ✓ Emoji
 *  ✓ Google Fonts (loaded in pdf.blade.php)
 *  ✓ External images (storage URLs resolve correctly)
 *  ✓ border-radius, box-shadow, gradients
 *  ✓ CSS calc(), var(), etc.
 *
 * All styling is still inline where possible so the template also works
 * in the browser admin preview (admin/orders/invoice.blade.php).
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── Bank logos: official brand assets in public/img/invoice/banks ─────────────
//  - bca.png     official BCA logo (GRUP BCA), from Wikimedia Commons
//                (source: bca.co.id brand assets). White background.
//  - seabank.png rasterized from seabank.svg — official SeaBank logo
//                (paths only, source: seabank.co.id via Wikimedia Commons).
//                Regenerate: node tools/rasterize-bank-logos.mjs
//  - blubca.png  official blu logo, downloaded from blubybcadigital.id
//                (_nuxt/logo-color). Transparent background.
// PNG (not SVG): SVG text using "Arial Black" gets clipped on the Linux
// server's headless Chrome (font missing -> wider fallback metrics).
// Embedded as data URIs so every renderer (Browsershot, DomPDF fallback,
// html2canvas PNG share) shows the identical artwork.
// Aspect ratios: bca.png 239×80 (2.99, white margins trimmed + made
// transparent), seabank.png 1260×405 (3.11), blubca.png 200×89 (2.25).
$bankLogoData = [];
foreach (['bca' => 'bca.png', 'sea' => 'seabank.png', 'blu' => 'blubca.png'] as $key => $file) {
    $path = public_path('img/invoice/banks/' . $file);
    if (is_file($path)) {
        $bankLogoData[$key] = 'data:image/png;base64,' . base64_encode((string) file_get_contents($path));
    }
}

$isCompact = count($doc['items'] ?? []) > 12;
$hasFee    = !empty($doc['item_has_fee']);
$qris      = $doc['qris'] ?? null;
$bank      = $doc['bank'] ?? null;
$isPaid    = $doc['paid'] ?? false;
// Payment section sizing: when only ONE method is shown it spans the full
// width, so it can (and must) be rendered much larger than in side-by-side
// mode — otherwise the QRIS / bank logo looks lost in a full-width card.
// In dual mode the QRIS card gets a bigger flex share (1.25 vs 1) so the
// QR code can be wider; bank logos are height-based so all three official
// logos share the same visual height despite different aspect ratios.
$hasBank   = !empty($bank['lines']) || !empty($bank['accounts'] ?? null);
// QRIS artwork fallback caps (portrait artwork must never push the card
// and the footer off the A4 page).
$qrisMaxW  = ($qris && $hasBank) ? '100mm' : '130mm';
$qrisMaxH  = ($qris && $hasBank) ? '100mm' : '105mm';
// Clean QR generated from the merchant payload: ~50mm of actual QR modules.
// The SVG includes a ~4-module quiet zone, so the displayed image is a bit
// larger than the QR itself.
$qrisQrImage = ($qris && function_exists('qris_qr_data_uri')) ? qris_qr_data_uri() : null;
$qrisQrSize  = ($qris && $hasBank) ? '62mm' : '70mm';
// Bank logos share one max box with object-fit: contain, so they render at
// uniform height (BCA 1.34, SeaBank 3.11, blu 2.25 aspect) without ever
// clipping or stretching. Heights are the visually uniform dimension.
$bankLogoH = $qris ? 38 : 48;
$bankLogoMaxW = $qris ? 110 : 150;
@endphp

@php
// Inline SVG icons (stroke style) — crisp in Chrome PDF, browser preview and
// html2canvas PNG; no emoji font needed.
$icons = [
    'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/>',
    'box'      => '<path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/>',
    'truck'    => '<rect x="1" y="6" width="14" height="10" rx="1.5"/><path d="M15 9h4l3 3v4h-7"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
    'list'     => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/>',
    'pin'      => '<path d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
    'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    'card'     => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
    'qr'       => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3M21 14v3M14 21h3M19 19v2h2"/>',
    'bank'     => '<path d="M3 10l9-6 9 6"/><path d="M5 10v8M9 10v8M15 10v8M19 10v8M3 20h18"/>',
    'alert'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5v5M12 16v.01"/>',
    'phone'    => '<path d="M20 12a8 8 0 01-11.8 7L4 20l1.1-4A8 8 0 1120 12z"/><path d="M9.5 9c0 3 2.5 5.5 5.5 5.5l1-1.4-2-1-.9.8c-.8-.3-1.6-1.1-1.9-1.9l.8-.9-1-2z"/>',
    'receipt'  => '<path d="M5 3h14v18l-2.3-1.6L14.4 21l-2.4-1.6-2.4 1.6L7.3 19.4 5 21z"/><path d="M8.5 8h7M8.5 12h7M8.5 16h4"/>',
    'wallet'   => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 9h18M16 13.5h3"/>',
    'cash'     => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.8"/><path d="M5.5 9.5h.01M18.5 14.5h.01"/>',
    'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
    'tag'      => '<path d="M3 3h8l10 10-8 8L3 11z"/><circle cx="8" cy="8" r="1.6"/>',
    'heart'    => '<path d="M12 20.5S3.5 15 3.5 9.4C3.5 6.4 5.8 4 8.7 4c1.4 0 2.6.5 3.3 1.4C12.7 4.5 13.9 4 15.3 4c2.9 0 5.2 2.4 5.2 5.4 0 5.6-8.5 11.1-8.5 11.1z"/>',
];
$icon = fn (string $name, int $size = 16, string $color = '#B45309') =>
    '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; display:block;">'.($icons[$name] ?? '').'</svg>';

$statusKey = $doc['status'] ?? ($isPaid ? 'paid' : 'unpaid');
[$badgeText, $badgeColor, $badgeBg] = match ($statusKey) {
    'paid' => ['LUNAS', '#15803D', '#F0FDF4'],
    'dp'   => ['DP DITERIMA', '#1D4ED8', '#EFF6FF'],
    default => ['BELUM BAYAR', '#C2410C', '#FFF4E8'],
};
@endphp

<div class="invoice-doc" data-layout="{{ $isCompact ? 'compact' : 'normal' }}"
     style="width: 100%; max-width: 100%; box-sizing: border-box; background: #FFFFFF; padding: 0;">

<div class="invoice-body">

    {{-- Top accent bar --}}
    <div style="height: 8px; background: #FFC800; border-radius: 0 0 6px 6px; margin-bottom: 12px;"></div>

    {{-- ═══ 1. HEADER ═══ --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">

        <div style="display: flex; align-items: center; gap: 14px; flex: 1;">
            @if(!empty($doc['logo']))
            <div style="border-right: 2px solid #FFC800; padding-right: 14px; flex-shrink: 0;">
                <img class="invoice-logo" src="{{ $doc['logo'] }}" alt="{{ $doc['brand'] }}"
                     style="width: 62px; height: 62px; object-fit: contain; border-radius: 6px; display: block;">
            </div>
            @endif
            <div>
                <div class="brand-name" style="font-size: 23px; font-weight: 900; color: #141414; line-height: 1.15; letter-spacing: -0.5px;">{{ $doc['brand'] }}</div>
                @if(!empty($doc['owner']))
                <div style="font-size: 12px; color: #64748B; margin-top: 3px;">{{ $doc['owner'] }}</div>
                @endif
                @if(!empty($doc['store_wa']))
                <div style="font-size: 12px; color: #475569; margin-top: 4px; display: flex; align-items: center; gap: 6px;">{!! $icon('phone', 15, '#16A34A') !!}<span>WhatsApp: {{ $doc['store_wa'] }}</span></div>
                @endif
            </div>
        </div>

        {{-- One right-aligned column: title, number, date and badge share the same right edge --}}
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0; padding-left: 20px; text-align: right;">
            <div class="invoice-title" style="font-size: 42px; font-weight: 900; color: #141414; letter-spacing: 1px; line-height: 1;">{{ $doc['title'] }}</div>
            <div style="font-size: 12px; color: #141414; font-weight: 800; line-height: 1.2;">Invoice #{{ $doc['number'] }}</div>
            <div style="font-size: 11.5px; color: #475569; display: flex; align-items: center; gap: 6px; line-height: 1.2;">{!! $icon('calendar', 14, '#475569') !!}<span>{{ $doc['date'] }}</span></div>
            <div>
                <span style="display: inline-block; padding: 4px 18px; border: 1.5px solid {{ $badgeColor }}; border-radius: 8px; color: {{ $badgeColor }}; font-weight: 800; font-size: 11.5px; letter-spacing: 0.5px; background: {{ $badgeBg }};">{{ $badgeText }}</span>
            </div>
        </div>
    </div>

    <div style="height: 4px; background: #FFC800; border-radius: 2px; margin: 8px 0 12px;"></div>


    {{-- ═══ 2. CUSTOMER + SUMMARY ═══ --}}
    <div style="display: flex; gap: 12px; margin-bottom: 12px;">

        <div style="flex: 1; min-width: 0; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden;">
            <div style="background: #F1F5F9; padding: 8px 12px; display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 800; color: #334155; letter-spacing: 0.4px;">
                {!! $icon('user', 16) !!} <span>DITAGIHKAN KEPADA</span>
            </div>
            <div class="info-card" style="padding: 9px 12px 10px;">
                <div style="font-size: 15px; font-weight: 800; color: #141414; margin-bottom: 3px;">{{ $doc['customer']['name'] }}</div>
                @if(!empty($doc['customer']['wa']))
                <div style="font-size: 12px; color: #475569; margin-top: 4px; display: flex; align-items: center; gap: 6px;">{!! $icon('phone', 14, '#16A34A') !!}<span>WhatsApp: {{ $doc['customer']['wa'] }}</span></div>
                @endif
                @if(!empty($doc['customer']['address']))
                <div style="font-size: 12px; color: #475569; margin-top: 4px; line-height: 1.5; display: flex; align-items: flex-start; gap: 6px;">{!! $icon('pin', 14, '#64748B') !!}<span>{{ $doc['customer']['address'] }}</span></div>
                @endif
            </div>
        </div>

        <div style="flex: 1; min-width: 0; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden;">
            <div style="background: #F1F5F9; padding: 8px 12px; display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 800; color: #334155; letter-spacing: 0.4px;">
                {!! $icon('box', 16) !!} <span>RINGKASAN</span>
            </div>
            <div class="info-card" style="padding: 7px 12px 8px;">
                @foreach($doc['shipping'] as $row)
                <div style="display: flex; align-items: center; font-size: 12px; color: #475569; padding: 3px 0;">
                    <span style="display: flex; align-items: center; gap: 7px; width: 112px; flex-shrink: 0;">
                        {!! $icon($loop->first ? 'truck' : 'list', 14, '#64748B') !!}
                        <span>{{ $row['label'] }}</span>
                    </span>
                    <span style="width: 14px; text-align: center; flex-shrink: 0;">:</span>
                    <span style="font-weight: 700; color: #141414;">{{ $row['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>


    {{-- ═══ 3. PRODUCT TABLE ═══ --}}
    @php $th = 'background: #FFC800; padding: 8px 10px; font-size: 12.5px; font-weight: 800; color: #141414;'; @endphp
    <table class="invoice-items" style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 10px;">
        <thead>
            <tr>
                <th class="items-th" style="width: {{ $hasFee ? '35%' : '40%' }}; text-align: left; border-radius: 12px 0 0 12px; {{ $th }}">Produk</th>
                <th class="items-th" style="width: 10%; text-align: center; {{ $th }}">Satuan</th>
                <th class="items-th" style="width: 8%; text-align: center; {{ $th }}">Qty</th>
                <th class="items-th" style="width: {{ $hasFee ? '16%' : '20%' }}; text-align: right; {{ $th }}">Harga</th>
                @if($hasFee)
                <th class="items-th" style="width: 13%; text-align: right; {{ $th }}">Fee item</th>
                @endif
                <th class="items-th" style="width: {{ $hasFee ? '18%' : '22%' }}; text-align: right; border-radius: 0 12px 12px 0; {{ $th }}">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doc['items'] as $item)
            <tr>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 10px; vertical-align: top; word-break: break-word; font-size: 12.5px;">{{ $item['name'] }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 10px; vertical-align: top; text-align: center; color: #475569; font-size: 12.5px;">{{ $item['unit'] ?: '-' }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 10px; vertical-align: top; text-align: center; font-size: 12.5px;">{{ $item['qty'] }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 10px; vertical-align: top; text-align: right; white-space: nowrap; font-size: 12.5px;">{{ $item['price'] }}</td>
                @if($hasFee)
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 10px; vertical-align: top; text-align: right; color: #92400E; white-space: nowrap; font-size: 12.5px;">{{ $item['fee'] ?? '-' }}</td>
                @endif
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 10px; vertical-align: top; text-align: right; font-weight: 800; white-space: nowrap; font-size: 12.5px;">{{ $item['subtotal'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    {{-- ═══ 4. TOTALS ═══ --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 12px;">
        <div class="invoice-totals-box" style="width: 58%; border: 1.5px solid #E2E8F0; border-radius: 12px; overflow: hidden; background: #FFFFFF;">
            @foreach($doc['totals'] as $row)
            @php
                $lbl = strtolower($row['label'] ?? '');
                $rowIcon = match (true) {
                    str_contains($lbl, 'ongkir') => 'truck',
                    str_contains($lbl, 'fee') => 'card',
                    str_contains($lbl, 'diskon') => 'tag',
                    default => 'receipt',
                };
            @endphp
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 6px 14px; font-size: 12.5px; {{ !empty($row['green']) ? 'color:#166534;' : 'color:#334155;' }}">
                <span style="display: flex; align-items: center; gap: 7px;">{!! $icon($rowIcon, 14, !empty($row['green']) ? '#166534' : '#64748B') !!}<span>{{ $row['label'] }}</span></span>
                <span style="{{ !empty($row['green']) ? 'font-weight:700;' : '' }} white-space: nowrap;">{{ $row['value'] }}</span>
            </div>
            @endforeach
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 8px 14px; background: #FFC800; font-size: 15px; font-weight: 900;">
                <span style="display: flex; align-items: center; gap: 7px;">{!! $icon('cash', 15, '#141414') !!}<span>{{ $doc['total']['label'] }}</span></span>
                <span style="white-space: nowrap;">{{ $doc['total']['value'] }}</span>
            </div>
            @foreach(($doc['paid_rows'] ?? []) as $row)
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 6px 14px; font-size: 12.5px; color: #166534; border-top: 1px solid #F1F5F9;">
                <span style="display: flex; align-items: center; gap: 7px;">{!! $icon('wallet', 14, '#166534') !!}<span>{{ $row['label'] }}</span></span>
                <span style="font-weight: 700; white-space: nowrap;">{{ $row['value'] }}</span>
            </div>
            @endforeach
            @if($doc['left'])
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 8px 14px; background: #FEF3C7; font-size: 15px; font-weight: 800;">
                <span style="display: flex; align-items: center; gap: 7px;">{!! $icon('clock', 15, '#141414') !!}<span>{{ $doc['left']['label'] }}</span></span>
                <span style="white-space: nowrap;">{{ $doc['left']['value'] }}</span>
            </div>
            @endif
        </div>
    </div>


    {{-- ═══ 5. CARA PEMBAYARAN ═══ --}}
    <div style="border-top: 1.5px solid #FFE08A; margin-bottom: 10px;"></div>
    <div class="section-header" style="font-size: 14px; font-weight: 900; color: #141414; letter-spacing: 0.3px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
        <span style="display: flex; align-items: center; gap: 9px;">{!! $icon('card', 20) !!} CARA PEMBAYARAN</span>
        @if(!$isPaid && !empty($doc['pay_amount']))
        <span style="font-size: 12px; font-weight: 700; color: #475569; display: inline-flex; align-items: center; gap: 6px;">{!! $icon('cash', 15, '#B45309') !!}<span>Nominal transfer: <span style="font-weight: 900; color: #141414;">{{ $doc['pay_amount'] }}</span></span></span>
        @endif
    </div>

    @if($isPaid)
        <div style="font-size: 13px; color: #15803D; font-weight: 700; padding: 10px 0;">
            ✅ Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada ' . $doc['paid_at'] : '' }}. Terima kasih.
        </div>

    @elseif($qris || ($bank && !empty($bank['lines'])))
        <div style="display: flex; gap: 12px; margin-bottom: 10px; align-items: stretch; page-break-inside: avoid;">

            {{-- QRIS --}}
            @if($qris)
            <div style="{{ ($bank && !empty($bank['lines'])) ? 'flex: 52;' : 'width: 100%;' }} min-width: 0; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px; display: flex; flex-direction: column;">
                <div class="pay-card-body" style="padding: 10px 12px 12px; text-align: center; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div style="display: inline-flex; align-items: center; gap: 8px; background: #FFC800; border-radius: 10px; padding: 6px 14px; margin-bottom: 10px;">
                        {!! $icon('qr', 18, '#141414') !!}
                        <span style="font-size: 13px; font-weight: 900; color: #141414; letter-spacing: 0.3px;">PEMBAYARAN QRIS</span>
                    </div>
                    <div style="border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 6px; background: #FFFFFF; display: inline-block; line-height: 0;">
                        @if($qrisQrImage)
                        <img class="qris-invoice-image qris-generated"
                             src="{{ $qrisQrImage }}"
                             alt="QRIS {{ $qris['merchant'] ?? 'JASTIP NITIPDIEND' }}"
                             style="display: block; width: {{ $qrisQrSize }}; height: {{ $qrisQrSize }}; object-fit: contain; background: #FFFFFF;">
                        @elseif(!empty($qris['image']))
                        <img class="qris-invoice-image"
                             src="{{ $qris['image'] }}"
                             alt="QRIS {{ $qris['merchant'] ?? 'JASTIP NITIPDIEND' }}"
                             style="display: block; width: 100%; max-width: {{ $qrisMaxW }}; max-height: {{ $qrisMaxH }}; height: auto; object-fit: contain; background: #FFFFFF;">
                        @endif
                    </div>
                    <div style="font-size: 14px; font-weight: 900; color: #141414; margin-top: 8px; letter-spacing: 0.3px;">{{ !empty($qris['merchant']) ? $qris['merchant'] : 'JASTIP NITIPDIEND' }}</div>
                    <div style="display: inline-block; margin-top: 9px; padding: 3px 16px; background: #E5E7EB; border-radius: 20px; font-size: 10.5px; color: #475569; font-weight: 600; letter-spacing: 0.5px;">SCAN / UPLOAD QRIS</div>
                </div>
            </div>
            @endif

            {{-- TRANSFER REKENING --}}
            @if($bank && !empty($bank['lines']))
            <div style="{{ $qris ? 'flex: 48;' : 'width: 100%;' }} min-width: 0; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px; display: flex; flex-direction: column;">
                <div class="pay-card-body" style="padding: 12px 14px; flex: 1; display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 9px; flex-shrink: 0;">
                        <span style="display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; background: #FFE58A; border-radius: 9px;">{!! $icon('bank', 18, '#7C4A03') !!}</span>
                        <span style="font-size: 13px; font-weight: 900; color: #141414; letter-spacing: 0.3px;">TRANSFER REKENING</span>
                    </div>
                    {{-- Entries vertically centered in the space below the header --}}
                    <div style="flex: 1; min-height: 0; display: flex; flex-direction: column; justify-content: center;">
                    @if(!empty($bank['accounts']))
                        @foreach($bank['accounts'] as $idx => $account)
                        @php
                            $bk = strtolower($account['bank'] ?? '');
                            if (str_contains($bk, 'blu')) {
                                $logo = $bankLogoData['blu'] ?? null;
                            } elseif (str_contains($bk, 'sea')) {
                                $logo = $bankLogoData['sea'] ?? null;
                            } else {
                                $logo = $bankLogoData['bca'] ?? null;
                            }
                        @endphp
                        <div style="{{ $idx > 0 ? 'border-top: 1px solid #E2E8F0; margin-top: 9px; padding-top: 9px;' : '' }} display: flex; align-items: center; gap: 10px;">
                            <div style="width: {{ $bankLogoMaxW }}px; flex-shrink: 0; display: flex; align-items: center;">
                                @if($logo)
                                <img class="bank-logo" src="{{ $logo }}" alt="{{ $account['bank'] }}"
                                     style="max-width: {{ $bankLogoMaxW }}px; max-height: {{ $bankLogoH }}px; width: auto; height: auto; object-fit: contain; display: block;">
                                @endif
                            </div>
                            <div style="min-width: 0;">
                                <div style="font-size: 13.5px; font-weight: 800; color: #141414; line-height: 1.3;">{{ $account['bank'] }}</div>
                                <div style="font-size: 12px; color: #334155; margin-top: 2px; line-height: 1.4;"><span style="font-weight: 700; white-space: nowrap;">{{ $account['number'] }}</span> <span style="white-space: nowrap;">a/n {{ $account['name'] }}</span></div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        @foreach($bank['lines'] as $line)
                        <div style="{{ !$loop->first ? 'border-top: 1px solid #E2E8F0; margin-top: 8px; padding-top: 8px;' : '' }} font-size: 12px; color: #475569; line-height: 1.45;">{{ $line }}</div>
                        @endforeach
                    @endif
                    </div>
                </div>
            </div>
            @endif

        </div>

        <div style="background: #FFEDE0; border-radius: 10px; padding: 9px 14px; margin-top: 2px; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; text-align: center; gap: 10px;">
            {!! $icon('alert', 20, '#C2410C') !!}
            <span style="font-size: 11.5px; font-weight: 800; color: #C2410C; line-height: 1.5; letter-spacing: 0.1px;">
                TRANSFER HANYA KE REKENING / QRIS DI ATAS. SELAIN ITU DI LUAR TANGGUNG JAWAB KAMI.
            </span>
        </div>

    @else
        <div style="font-size: 12px; color: #475569; padding: 6px 0;">{{ $doc['payment_text'] }}</div>
    @endif

</div>{{-- /.invoice-body --}}

    {{-- ═══ 6. FOOTER ═══ --}}
    <div class="invoice-footer">
        <div style="display: flex; align-items: center; gap: 12px; margin-top: 12px;">
            <div style="flex: 1; height: 1px; background: #CBD5E1;"></div>
            <div style="font-size: 12px; color: #475569; white-space: nowrap; display: flex; align-items: center; gap: 6px;">{!! $icon('heart', 14, '#E11D48') !!}<span>{{ $doc['footer'] }}</span></div>
            <div style="flex: 1; height: 1px; background: #CBD5E1;"></div>
        </div>
        <div style="height: 8px; background: #FFC800; border-radius: 6px 6px 0 0; margin-top: 12px;"></div>
    </div>

</div>
