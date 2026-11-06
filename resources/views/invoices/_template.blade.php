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

// ── Bank logos: SVG data URIs ─────────────────────────────────────────────────
// BCA — official blue shield/flower mark + bold BCA
$bcaSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 340 100" width="340" height="100">'
    // Shield outline (scaled to fit 90px height)
    . '<path d="M5,5 L75,5 Q88,5 88,18 L88,68 Q88,88 58,96 L40,100 Q22,96 5,88 L5,18 Q5,5 18,5 Z" fill="#FFFFFF" stroke="#1A3A8F" stroke-width="4"/>'
    // Flower center
    . '<circle cx="46" cy="38" r="12" fill="#1A3A8F"/>'
    // Side bulbs
    . '<circle cx="29" cy="50" r="10" fill="#1A3A8F"/>'
    . '<circle cx="63" cy="50" r="10" fill="#1A3A8F"/>'
    // Trunk
    . '<rect x="40" y="57" width="12" height="16" rx="2" fill="#1A3A8F"/>'
    // Base
    . '<rect x="24" y="71" width="44" height="6" rx="2" fill="#1A3A8F"/>'
    // GRUP BCA tiny text
    . '<text x="46" y="90" font-family="Arial,sans-serif" font-size="8" font-weight="600" fill="#1A3A8F" text-anchor="middle">GRUP BCA</text>'
    // BCA bold wordmark
    . '<text x="105" y="82" font-family="Arial Black,Arial,sans-serif" font-size="78" font-weight="900" fill="#1A3A8F">BCA</text>'
    . '</svg>';
$bcaLogo = 'data:image/svg+xml;base64,' . base64_encode($bcaSvg);

// SeaBank — orange circle S + SeaBank text + blue wave
$seaSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 380 110" width="380" height="110">'
    // Orange circle
    . '<circle cx="52" cy="52" r="48" fill="#FF6200"/>'
    // White S
    . '<text x="52" y="74" font-family="Arial Black,Arial,sans-serif" font-size="56" font-weight="900" fill="#FFFFFF" text-anchor="middle">S</text>'
    // SeaBank orange text
    . '<text x="115" y="68" font-family="Arial,Helvetica,sans-serif" font-size="52" font-weight="800" fill="#FF6200">SeaBank</text>'
    // Blue waves
    . '<path d="M115,84 Q150,73 185,84 Q220,95 255,84 Q290,73 325,84 Q350,90 370,84" fill="none" stroke="#1A6EC8" stroke-width="5" stroke-linecap="round"/>'
    . '<path d="M115,98 Q150,87 185,98 Q220,109 255,98 Q290,87 325,98" fill="none" stroke="#1A6EC8" stroke-width="3.5" stroke-linecap="round" opacity="0.5"/>'
    . '</svg>';
$seaLogo = 'data:image/svg+xml;base64,' . base64_encode($seaSvg);

// blu by BCA Digital — horizontal: teal "blu" + small BCA digital badge side by side
$bluSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 80" width="320" height="80">'
    // "blu" teal wordmark
    . '<text x="4" y="62" font-family="Arial Black,Arial,sans-serif" font-size="64" font-weight="900" fill="#00C8C8">blu</text>'
    // Small BCA shield
    . '<path d="M148,14 L178,14 Q184,14 184,20 L184,52 Q184,60 168,64 L163,66 Q158,64 142,60 L142,20 Q142,14 148,14 Z" fill="#FFFFFF" stroke="#1A3A8F" stroke-width="2.5"/>'
    . '<circle cx="163" cy="30" r="6" fill="#1A3A8F"/>'
    . '<circle cx="156" cy="37" r="5" fill="#1A3A8F"/>'
    . '<circle cx="170" cy="37" r="5" fill="#1A3A8F"/>'
    . '<rect x="159" y="41" width="7" height="9" rx="1.5" fill="#1A3A8F"/>'
    . '<rect x="152" y="48" width="22" height="4" rx="2" fill="#1A3A8F"/>'
    . '<text x="163" y="60" font-family="Arial,sans-serif" font-size="5" fill="#1A3A8F" text-anchor="middle">GRUP BCA</text>'
    // "BCA digital" text
    . '<text x="192" y="40" font-family="Arial Black,Arial,sans-serif" font-size="22" font-weight="900" fill="#1A3A8F">BCA</text>'
    . '<text x="192" y="62" font-family="Arial,Helvetica,sans-serif" font-size="22" font-weight="700" fill="#00C8C8">digital</text>'
    . '</svg>';
$bluLogo = 'data:image/svg+xml;base64,' . base64_encode($bluSvg);

$isCompact = count($doc['items'] ?? []) > 12;
$hasFee    = !empty($doc['item_has_fee']);
$qris      = $doc['qris'] ?? null;
$bank      = $doc['bank'] ?? null;
$isPaid    = $doc['paid'] ?? false;
// Payment section sizing: when only ONE method is shown it spans the full
// width, so it can (and must) be rendered much larger than in side-by-side
// mode — otherwise a 100px bank logo / 85mm QRIS looks lost in a full-width
// card. Dual mode caps QRIS to its column width.
$hasBank   = !empty($bank['lines']) || !empty($bank['accounts'] ?? null);
$qrisMaxW  = ($qris && $hasBank) ? '92mm' : '125mm';
$bankLogoW  = $hasBank ? ($qris ? 150 : 210) : 150;
@endphp

<div class="invoice-doc" data-layout="{{ $isCompact ? 'compact' : 'normal' }}"
     style="width: 100%; background: #FFFFFF; padding: 0;">

    {{-- ══════════════════════════════════════════════════════════════
         1. HEADER
    ══════════════════════════════════════════════════════════════ --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">

        {{-- LEFT: brand --}}
        <div style="display: flex; align-items: center; gap: 14px; flex: 1;">
            @if(!empty($doc['logo']))
            <div style="border-right: 2px solid #E2E8F0; padding-right: 14px; flex-shrink: 0;">
                <img class="invoice-logo"
                     src="{{ $doc['logo'] }}"
                     alt="{{ $doc['brand'] }}"
                     style="width: 72px; height: 72px; object-fit: contain; border-radius: 6px; display: block;">
            </div>
            @endif
            <div>
                <div class="brand-name" style="font-size: 22px; font-weight: 900; color: #141414; line-height: 1.15; letter-spacing: -0.5px;">{{ $doc['brand'] }}</div>
                @if(!empty($doc['owner']))
                <div style="font-size: 11.5px; color: #64748B; margin-top: 4px;">{{ $doc['owner'] }}</div>
                @endif
                @if(!empty($doc['store_wa']))
                <div style="font-size: 11.5px; color: #475569; margin-top: 3px;">📱 WhatsApp: {{ $doc['store_wa'] }}</div>
                @endif
            </div>
        </div>

        {{-- RIGHT: invoice meta --}}
        <div style="text-align: right; flex-shrink: 0; padding-left: 20px;">
            <div class="invoice-title" style="font-size: 44px; font-weight: 900; color: #141414; letter-spacing: 2px; line-height: 1;">{{ $doc['title'] }}</div>
            <div style="font-size: 12px; color: #475569; margin-top: 6px; font-weight: 500;">Invoice #{{ $doc['number'] }}</div>
            <div style="font-size: 11.5px; color: #475569; margin-top: 3px;">📅 {{ $doc['date'] }}</div>
            <div style="margin-top: 8px;">
                @if($isPaid)
                    <span style="display: inline-block; padding: 4px 16px; border: 2px solid #15803D; border-radius: 4px; color: #15803D; font-weight: 800; font-size: 11px; letter-spacing: 1px; background: #F0FDF4;">LUNAS</span>
                @else
                    <span style="display: inline-block; padding: 4px 16px; border: 2px solid #D97706; border-radius: 4px; color: #D97706; font-weight: 800; font-size: 11px; letter-spacing: 0.5px; background: #FFFBEB;">BELUM BAYAR</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Yellow divider --}}
    <div style="height: 5px; background: #FFC800; border-radius: 2px; margin: 10px 0 16px;"></div>


    {{-- ══════════════════════════════════════════════════════════════
         2. CUSTOMER INFO + ORDER SUMMARY
    ══════════════════════════════════════════════════════════════ --}}
    <div style="display: flex; gap: 12px; margin-bottom: 16px;">

        {{-- LEFT: Ditagihkan Kepada --}}
        <div class="info-card" style="flex: 1; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px 14px;">
            <div style="font-size: 10px; font-weight: 800; color: #64748B; letter-spacing: 1px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <span>👤</span> DITAGIHKAN KEPADA
            </div>
            <div style="font-size: 15px; font-weight: 800; color: #141414; margin-bottom: 4px;">{{ $doc['customer']['name'] }}</div>
            @if(!empty($doc['customer']['wa']))
            <div style="font-size: 12px; color: #475569; margin-top: 3px;">📱 WhatsApp: {{ $doc['customer']['wa'] }}</div>
            @endif
            @if(!empty($doc['customer']['address']))
            <div style="font-size: 12px; color: #475569; margin-top: 3px; line-height: 1.5;">📍 {{ $doc['customer']['address'] }}</div>
            @endif
        </div>

        {{-- RIGHT: Ringkasan --}}
        <div class="info-card" style="flex: 1; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px 14px;">
            <div style="font-size: 10px; font-weight: 800; color: #64748B; letter-spacing: 1px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <span>📦</span> RINGKASAN
            </div>
            @foreach($doc['shipping'] as $row)
            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #475569; padding: 3px 0;">
                <span style="display: flex; align-items: center; gap: 5px;">
                    <span>{{ $loop->first ? '🚚' : '📋' }}</span>
                    {{ $row['label'] }}
                </span>
                <span style="font-weight: 700; color: #141414;">: {{ $row['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════
         3. PRODUCT TABLE
    ══════════════════════════════════════════════════════════════ --}}
    <table class="invoice-items" style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <thead>
            <tr>
                <th class="items-th" style="width: {{ $hasFee ? '35%' : '40%' }}; background: #FFC800; text-align: left; padding: 9px 10px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Produk</th>
                <th class="items-th" style="width: 10%; background: #FFC800; text-align: center; padding: 9px 6px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Satuan</th>
                <th class="items-th" style="width: 8%; background: #FFC800; text-align: center; padding: 9px 6px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Qty</th>
                <th class="items-th" style="width: {{ $hasFee ? '16%' : '20%' }}; background: #FFC800; text-align: right; padding: 9px 8px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Harga</th>
                @if($hasFee)
                <th class="items-th" style="width: 13%; background: #FFC800; text-align: right; padding: 9px 7px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Fee item</th>
                @endif
                <th class="items-th" style="width: {{ $hasFee ? '18%' : '22%' }}; background: #FFC800; text-align: right; padding: 9px 10px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doc['items'] as $item)
            <tr>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 10px; vertical-align: top; word-break: break-word; font-size: 12.5px;">{{ $item['name'] }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 6px; text-align: center; color: #475569; font-size: 12.5px;">{{ $item['unit'] ?: '-' }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 6px; text-align: center; font-size: 12.5px;">{{ $item['qty'] }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px; text-align: right; white-space: nowrap; font-size: 12.5px;">{{ $item['price'] }}</td>
                @if($hasFee)
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 7px; text-align: right; color: #92400E; white-space: nowrap; font-size: 12.5px;">{{ $item['fee'] ?? '-' }}</td>
                @endif
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 8px 10px; text-align: right; font-weight: 700; white-space: nowrap; font-size: 12.5px;">{{ $item['subtotal'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    {{-- ══════════════════════════════════════════════════════════════
         4. PAYMENT SUMMARY BOX (right-aligned 58%)
    ══════════════════════════════════════════════════════════════ --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
        <div class="invoice-totals-box" style="width: 58%; border: 1.5px solid #CBD5E1; border-radius: 8px; overflow: hidden;">
            @foreach($doc['totals'] as $row)
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 7px 13px; border-bottom: 1px solid #F1F5F9; font-size: 12px; {{ !empty($row['green']) ? 'color:#166534;' : 'color:#475569;' }}">
                <span>{{ $row['label'] }}</span>
                <span style="{{ !empty($row['green']) ? 'font-weight:700;' : '' }} white-space: nowrap;">{{ $row['value'] }}</span>
            </div>
            @endforeach
            {{-- TOTAL BAYAR --}}
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 9px 13px; background: #FFC800; border-top: 2px solid #141414; font-size: 14px; font-weight: 900;">
                <span>{{ $doc['total']['label'] }}</span>
                <span style="white-space: nowrap;">{{ $doc['total']['value'] }}</span>
            </div>
            {{-- Sisa Bayar --}}
            @if($doc['left'])
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 9px 13px; background: #FEF3C7; border-top: 1px solid #FDE68A; font-size: 15px; font-weight: 800;">
                <span>{{ $doc['left']['label'] }}</span>
                <span style="white-space: nowrap;">{{ $doc['left']['value'] }}</span>
            </div>
            @endif
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════
         5. CARA PEMBAYARAN
    ══════════════════════════════════════════════════════════════ --}}
    <div style="border-top: 1.5px solid #E2E8F0; margin-bottom: 12px;"></div>
    <div class="section-header" style="font-size: 12px; font-weight: 800; color: #141414; letter-spacing: 0.3px; margin-bottom: 10px; display: flex; align-items: center; gap: 7px;">
        💳 CARA PEMBAYARAN
    </div>

    @if($isPaid)
        <div style="font-size: 13px; color: #15803D; font-weight: 700; padding: 10px 0;">
            ✅ Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada ' . $doc['paid_at'] : '' }}. Terima kasih.
        </div>

    @elseif($qris || ($bank && !empty($bank['lines'])))
        <div style="display: flex; gap: 12px; margin-bottom: 10px; align-items: stretch;">

            {{-- ── LEFT: QRIS ──────────────────────────────────── --}}
            @if($qris)
            <div style="{{ ($bank && !empty($bank['lines'])) ? 'flex: 1;' : 'width: 100%;' }} border: 1.5px solid #E2E8F0; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column;">
                {{-- yellow header --}}
                <div style="background: #FFC800; padding: 9px 13px; display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    <span style="font-size: 16px;">▦</span>
                    <span style="font-size: 13px; font-weight: 900; color: #141414; letter-spacing: 0.3px;">PEMBAYARAN QRIS</span>
                </div>
                {{-- QR area --}}
                <div class="pay-card-body" style="padding: 8px 10px 12px; text-align: center; background: #FFFFFF; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    @if(!empty($qris['image']))
                    <img class="qris-invoice-image"
                         src="{{ $qris['image'] }}"
                         alt="QRIS JASTIP NITIPDIEND"
                         style="display: block; width: 100%; max-width: {{ $qrisMaxW }}; height: auto; object-fit: contain; background: #FFFFFF;">
                    @endif
                    <div style="font-size: 13px; font-weight: 900; color: #141414; margin-top: 8px; letter-spacing: 0.3px;">JASTIP NITIPDIEND</div>
                    <div style="display: inline-block; margin-top: 4px; padding: 3px 14px; background: #F1F5F9; border-radius: 20px; font-size: 10.5px; color: #64748B; font-weight: 600; letter-spacing: 0.5px;">SCAN / UPLOAD QRIS</div>
                </div>
            </div>
            @endif

            {{-- ── RIGHT: TRANSFER REKENING ───────────────────── --}}
            @if($bank && !empty($bank['lines']))
            <div style="{{ $qris ? 'flex: 1;' : 'width: 100%;' }} border: 1.5px solid #E2E8F0; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column;">
                {{-- header --}}
                <div style="background: #FFFFFF; padding: 9px 14px; border-bottom: 1.5px solid #E2E8F0; display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    <span style="font-size: 16px;">🏦</span>
                    <span style="font-size: 13px; font-weight: 900; color: #141414; letter-spacing: 0.3px;">TRANSFER REKENING</span>
                </div>
                {{-- bank list --}}
                <div class="pay-card-body" style="padding: 12px 14px; background: #FFFFFF; flex: 1;">
                    @if(!empty($bank['accounts']))
                        @foreach($bank['accounts'] as $idx => $account)
                        @php
                            $bk = strtolower($account['bank'] ?? '');
                            $lw = $bankLogoW.'px';
                            if (str_contains($bk, 'blu')) {
                                $logo = $bluLogo; $lh = round($bankLogoW * 80 / 320).'px';
                            } elseif (str_contains($bk, 'sea')) {
                                $logo = $seaLogo; $lh = round($bankLogoW * 110 / 380).'px';
                            } else {
                                $logo = $bcaLogo; $lh = round($bankLogoW * 100 / 340).'px';
                            }
                        @endphp
                        <div style="{{ $idx > 0 ? 'border-top: 1px solid #F1F5F9; margin-top: 10px; padding-top: 10px;' : '' }} display: flex; align-items: center; gap: 12px;">
                            <img class="bank-logo"
                                 src="{{ $logo }}"
                                 alt="{{ $account['bank'] }}"
                                 style="width: {{ $lw }}; height: {{ $lh }}; object-fit: contain; flex-shrink: 0; display: block;">
                            <div>
                                <div style="font-size: 14px; font-weight: 800; color: #141414; line-height: 1.3;">{{ $account['bank'] }}</div>
                                <div style="font-size: 13px; font-weight: 700; color: #334155; margin-top: 3px; line-height: 1.4;">{{ $account['number'] }} a/n {{ $account['name'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        @foreach($bank['lines'] as $line)
                        <div style="{{ !$loop->first ? 'border-top: 1px solid #F1F5F9; margin-top: 8px; padding-top: 8px;' : '' }} font-size: 12px; color: #475569; line-height: 1.45;">{{ $line }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- Warning banner --}}
        <div style="background: #FFF7ED; border: 1.5px solid #FDBA74; border-radius: 8px; padding: 9px 13px; margin-top: 4px; margin-bottom: 6px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 18px; flex-shrink: 0;">⚠️</span>
            <span style="font-size: 11.5px; font-weight: 800; color: #C2410C; line-height: 1.5; letter-spacing: 0.1px;">
                TRANSFER HANYA KE REKENING / QRIS DI ATAS. SELAIN ITU DI LUAR TANGGUNG JAWAB KAMI.
            </span>
        </div>

    @else
        <div style="font-size: 12px; color: #475569; padding: 6px 0;">{{ $doc['payment_text'] }}</div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════
         6. FOOTER
    ══════════════════════════════════════════════════════════════ --}}
    <div style="height: 1px; background: #E2E8F0; margin-top: 14px; margin-bottom: 10px;"></div>
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="flex: 1; height: 1px; background: #CBD5E1;"></div>
        <div style="font-size: 11.5px; color: #64748B; white-space: nowrap;">{{ $doc['footer'] }}</div>
        <div style="flex: 1; height: 1px; background: #CBD5E1;"></div>
    </div>

</div>
