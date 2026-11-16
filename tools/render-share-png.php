<?php

/**
 * Render the share PNG exactly as /i/{token}/gambar does, but with a local
 * fixture doc instead of a database order — for previewing slide kualitas
 * gambar yang dikirim ke WhatsApp.
 *
 * Usage: php tools/render-share-png.php
 * Output: storage/app/invoice-qa/share-png.png
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\InvoicePdfRenderer;

$bank = ['lines' => bank_account_lines(), 'accounts' => config('invoice.accounts', []), 'text' => bank_account_text()];

$items = [
    ['name' => 'Hampers SOG - Sarong Songket', 'unit' => 'pcs', 'qty' => 3, 'price' => format_price(103000), 'subtotal' => format_price(309000)],
];
foreach (['Blue Sky / Standar', 'Mauve / Standar', 'Matcha / Standar', 'Navy / Standar', 'Pale Cream / Standar'] as $variant) {
    $items[] = ['name' => 'Hampers SOG Hijab / Royal Garden / ' . $variant, 'unit' => 'pcs', 'qty' => 1, 'price' => format_price(65000), 'subtotal' => format_price(65000)];
}

$logo = '';
// Logo yang sama dengan prod: storage/app/public/settings/logo.jpg
$logoFile = storage_path('app/public/settings/logo.jpg');
if (is_file($logoFile)) {
    $ext = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
    $mime = match ($ext) {
        'jpg', 'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        default => 'image/png',
    };
    $logo = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoFile));
}

$doc = [
    'brand' => 'NITIPDIEND',
    'logo' => $logo,
    'owner' => 'Nabila Adriyana',
    'store_wa' => '6282336534191',
    'title' => 'TAGIHAN',
    'number' => 'ND20261104-2A814FDA',
    'date' => '04 Nov 2026, 16:47 WIB',
    'status' => 'dp',
    'status_text' => 'DP DITERIMA',
    'status_color' => '#1D4ED8',
    'customer' => ['name' => 'Bu Hesty', 'wa' => '085771216563', 'address' => 'Jakarta'],
    'shipping' => [
        ['label' => 'Ekspedisi', 'value' => '-'],
        ['label' => 'Jumlah item', 'value' => '8 pcs'],
    ],
    'items' => $items,
    'totals' => [
        ['label' => 'Subtotal', 'value' => format_price(520000)],
        ['label' => 'Ongkir', 'value' => format_price(0)],
    ],
    'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price(520000)],
    'left' => ['label' => 'Sisa Bayar', 'value' => format_price(320000)],
    'paid_rows' => [['label' => 'Deposit masuk', 'value' => '-' . format_price(200000), 'green' => true]],
    'paid' => false,
    'payment_text' => 'Silakan transfer, lalu kirim bukti via WhatsApp.',
    'qris' => null,
    'bank' => $bank,
    'footer' => 'Terima kasih telah menitip di NITIPDIEND',
];

$html = view('invoices.share', ['doc' => $doc])->render();

$png = app(InvoicePdfRenderer::class)->screenshotPng($html);

$path = storage_path('app/invoice-qa/share-png.png');
file_put_contents($path, $png);

echo "rendered {$path}\n";
echo 'bytes : ' . strlen($png) . "\n";
echo "starts: " . substr($png, 0, 8) . "\n";
