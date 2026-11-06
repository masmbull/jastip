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

// ── Bank logos: official PNG renders of the SVG files in public/img/invoice/banks ──
// PNG (not SVG): the SVGs use "Arial Black" text which is missing on the
// Linux server's headless Chrome — fallback metrics made the wordmarks
// overflow the viewBox and get clipped ("SeaBank" -> "SeaB"). PNG bakes
// the correct shapes. Embedded as data URIs so every renderer
// (Browsershot, DomPDF fallback, html2canvas PNG share) is identical.
// Regenerate after editing an SVG: node tools/rasterize-bank-logos.mjs
// Aspect ratios: bca.png 1560×600 (2.60), seabank.png 1440×480 (3.00),
// blubca.png 1200×900 (1.33, stacked "blu" + BCA digital).
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
$qrisMaxW  = ($qris && $hasBank) ? '100mm' : '140mm';
// Height cap so a portrait QRIS image can never push the payment card
// (and the footer) off the A4 page. Square QRIS stays full width.
$qrisMaxH  = ($qris && $hasBank) ? '100mm' : '120mm';
// Bank logos are max-box sized (never fixed width/height) so they always
// fit their column without distortion or clipping.
$bankLogoH = $qris ? 46 : 64;
$bankLogoMaxW = $qris ? 135 : 190;
@endphp

<div class="invoice-doc" data-layout="{{ $isCompact ? 'compact' : 'normal' }}"
     style="width: 100%; max-width: 100%; box-sizing: border-box; background: #FFFFFF; padding: 0;">

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
                     style="width: 60px; height: 60px; object-fit: contain; border-radius: 6px; display: block;">
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
    <div style="height: 5px; background: #FFC800; border-radius: 2px; margin: 6px 0 10px;"></div>


    {{-- ══════════════════════════════════════════════════════════════
         2. CUSTOMER INFO + ORDER SUMMARY
    ══════════════════════════════════════════════════════════════ --}}
    <div style="display: flex; gap: 12px; margin-bottom: 10px;">

        {{-- LEFT: Ditagihkan Kepada --}}
        <div class="info-card" style="flex: 1; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 9px 12px;">
            <div style="font-size: 10px; font-weight: 800; color: #64748B; letter-spacing: 1px; margin-bottom: 5px; display: flex; align-items: center; gap: 6px;">
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
        <div class="info-card" style="flex: 1; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 9px 12px;">
            <div style="font-size: 10px; font-weight: 800; color: #64748B; letter-spacing: 1px; margin-bottom: 5px; display: flex; align-items: center; gap: 6px;">
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
    <table class="invoice-items" style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
        <thead>
            <tr>
                <th class="items-th" style="width: {{ $hasFee ? '35%' : '40%' }}; background: #FFC800; text-align: left; padding: 7px 10px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Produk</th>
                <th class="items-th" style="width: 10%; background: #FFC800; text-align: center; padding: 7px 6px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Satuan</th>
                <th class="items-th" style="width: 8%; background: #FFC800; text-align: center; padding: 7px 6px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Qty</th>
                <th class="items-th" style="width: {{ $hasFee ? '16%' : '20%' }}; background: #FFC800; text-align: right; padding: 7px 8px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Harga</th>
                @if($hasFee)
                <th class="items-th" style="width: 13%; background: #FFC800; text-align: right; padding: 7px 7px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Fee item</th>
                @endif
                <th class="items-th" style="width: {{ $hasFee ? '18%' : '22%' }}; background: #FFC800; text-align: right; padding: 7px 10px; border-bottom: 2px solid #141414; font-size: 12px; font-weight: 800;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doc['items'] as $item)
            <tr>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 6px 10px; vertical-align: top; word-break: break-word; font-size: 12.5px;">{{ $item['name'] }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 6px 6px; text-align: center; color: #475569; font-size: 12.5px;">{{ $item['unit'] ?: '-' }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 6px 6px; text-align: center; font-size: 12.5px;">{{ $item['qty'] }}</td>
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 6px; text-align: right; white-space: nowrap; font-size: 12.5px;">{{ $item['price'] }}</td>
                @if($hasFee)
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 6px 7px; text-align: right; color: #92400E; white-space: nowrap; font-size: 12.5px;">{{ $item['fee'] ?? '-' }}</td>
                @endif
                <td class="items-td" style="border-bottom: 1px solid #E2E8F0; padding: 6px 10px; text-align: right; font-weight: 700; white-space: nowrap; font-size: 12.5px;">{{ $item['subtotal'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    {{-- ══════════════════════════════════════════════════════════════
         4. PAYMENT SUMMARY BOX (right-aligned 58%)
    ══════════════════════════════════════════════════════════════ --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
        <div class="invoice-totals-box" style="width: 58%; border: 1.5px solid #CBD5E1; border-radius: 8px; overflow: hidden;">
            @foreach($doc['totals'] as $row)
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 5px 13px; border-bottom: 1px solid #F1F5F9; font-size: 12px; {{ !empty($row['green']) ? 'color:#166534;' : 'color:#475569;' }}">
                <span>{{ $row['label'] }}</span>
                <span style="{{ !empty($row['green']) ? 'font-weight:700;' : '' }} white-space: nowrap;">{{ $row['value'] }}</span>
            </div>
            @endforeach
            {{-- TOTAL BAYAR --}}
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 7px 13px; background: #FFC800; border-top: 2px solid #141414; font-size: 14px; font-weight: 900;">
                <span>{{ $doc['total']['label'] }}</span>
                <span style="white-space: nowrap;">{{ $doc['total']['value'] }}</span>
            </div>
            {{-- Sisa Bayar --}}
            @if($doc['left'])
            <div class="totals-td" style="display: flex; justify-content: space-between; padding: 7px 13px; background: #FEF3C7; border-top: 1px solid #FDE68A; font-size: 15px; font-weight: 800;">
                <span>{{ $doc['left']['label'] }}</span>
                <span style="white-space: nowrap;">{{ $doc['left']['value'] }}</span>
            </div>
            @endif
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════
         5. CARA PEMBAYARAN
    ══════════════════════════════════════════════════════════════ --}}
    <div style="border-top: 1.5px solid #E2E8F0; margin-bottom: 8px;"></div>
    <div class="section-header" style="font-size: 12px; font-weight: 800; color: #141414; letter-spacing: 0.3px; margin-bottom: 6px; display: flex; align-items: center; gap: 7px;">
        💳 CARA PEMBAYARAN
    </div>

    @if($isPaid)
        <div style="font-size: 13px; color: #15803D; font-weight: 700; padding: 10px 0;">
            ✅ Pembayaran sudah diterima{{ !empty($doc['paid_at']) ? ' pada ' . $doc['paid_at'] : '' }}. Terima kasih.
        </div>

    @elseif($qris || ($bank && !empty($bank['lines'])))
        <div style="display: flex; gap: 12px; margin-bottom: 8px; align-items: stretch; page-break-inside: avoid;">

            {{-- ── LEFT: QRIS ──────────────────────────────────── --}}
            @if($qris)
            <div style="{{ ($bank && !empty($bank['lines'])) ? 'flex: 1.25;' : 'width: 100%;' }} min-width: 0; border: 1.5px solid #E2E8F0; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column;">
                {{-- yellow header --}}
                <div style="background: #FFC800; padding: 7px 13px; display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    <span style="font-size: 16px;">▦</span>
                    <span style="font-size: 13px; font-weight: 900; color: #141414; letter-spacing: 0.3px;">PEMBAYARAN QRIS</span>
                </div>
                {{-- QR area --}}
                <div class="pay-card-body" style="padding: 6px 10px 8px; text-align: center; background: #FFFFFF; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    @if(!empty($qris['image']))
                    <img class="qris-invoice-image"
                         src="{{ $qris['image'] }}"
                         alt="QRIS JASTIP NITIPDIEND"
                         style="display: block; width: 100%; max-width: {{ $qrisMaxW }}; max-height: {{ $qrisMaxH }}; height: auto; object-fit: contain; background: #FFFFFF;">
                    @endif
                    <div style="font-size: 13px; font-weight: 900; color: #141414; margin-top: 4px; letter-spacing: 0.3px;">JASTIP NITIPDIEND</div>
                    <div style="display: inline-block; margin-top: 2px; padding: 3px 14px; background: #F1F5F9; border-radius: 20px; font-size: 10.5px; color: #64748B; font-weight: 600; letter-spacing: 0.5px;">SCAN / UPLOAD QRIS</div>
                </div>
            </div>
            @endif

            {{-- ── RIGHT: TRANSFER REKENING ───────────────────── --}}
            @if($bank && !empty($bank['lines']))
            <div style="{{ $qris ? 'flex: 1;' : 'width: 100%;' }} min-width: 0; border: 1.5px solid #E2E8F0; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column;">
                {{-- header --}}
                <div style="background: #FFFFFF; padding: 7px 14px; border-bottom: 1.5px solid #E2E8F0; display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    <span style="font-size: 16px;">🏦</span>
                    <span style="font-size: 13px; font-weight: 900; color: #141414; letter-spacing: 0.3px;">TRANSFER REKENING</span>
                </div>
                {{-- bank list — centered vertically so the empty space is
                     split evenly above/below instead of piling up at the bottom --}}
                <div class="pay-card-body" style="padding: 10px 14px; background: #FFFFFF; flex: 1; display: flex; flex-direction: column; justify-content: center;">
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
                        <div style="{{ $idx > 0 ? 'border-top: 1px solid #F1F5F9; margin-top: 10px; padding-top: 10px;' : '' }} display: flex; align-items: center; gap: 12px;">
                            @if($logo)
                            <img class="bank-logo"
                                 src="{{ $logo }}"
                                 alt="{{ $account['bank'] }}"
                                 style="max-width: {{ $bankLogoMaxW }}px; max-height: {{ $bankLogoH }}px; width: auto; height: auto; flex-shrink: 0; display: block;">
                            @endif
                            <div style="min-width: 0;">
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

        {{-- Warning banner — centered horizontally & vertically --}}
        <div style="background: #FFF7ED; border: 1.5px solid #FDBA74; border-radius: 8px; padding: 7px 13px; margin-top: 2px; margin-bottom: 4px; display: flex; align-items: center; justify-content: center; text-align: center; gap: 10px;">
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
    <div style="height: 1px; background: #E2E8F0; margin-top: 8px; margin-bottom: 6px;"></div>
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="flex: 1; height: 1px; background: #CBD5E1;"></div>
        <div style="font-size: 11.5px; color: #64748B; white-space: nowrap;">{{ $doc['footer'] }}</div>
        <div style="flex: 1; height: 1px; background: #CBD5E1;"></div>
    </div>

</div>
