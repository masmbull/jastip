<?php

/**
 * Render the heavy "compact" variant through the real renderer
 * (InvoicePdfRenderer) and report page count + per-page text bounds.
 *
 * Usage: php heavy-order.php
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\InvoicePdfRenderer;

$bank = [
    'lines' => bank_account_lines(),
    'accounts' => config('invoice.accounts', []),
    'text' => bank_account_text(),
];

$ITEMS = (int)(getenv('ITEMS') ?: 14);

$items = [];
for ($i = 1; $i <= $ITEMS; $i++) {
    $items[] = [
        'name' => 'Produk Jastip Jepang Baris Ke-' . $i . ' (Sample)',
        'unit' => 'pcs', 'qty' => 1,
        'price' => format_price(50000 * $i),
        'subtotal' => format_price(50000 * $i),
    ];
}

$doc = array_merge([
    'brand' => 'NITIPDIEND',
    'logo' => '',
    'owner' => 'Nabila Adriyana',
    'store_wa' => '6282336534191',
    'title' => 'TAGIHAN',
    'number' => 'ND20261106-DE30962A',
    'date' => '06 Nov 2026, 03:03 WIB',
    'customer' => ['name' => 'Nabila Adriyana', 'wa' => '082336534191', 'address' => 'Jakardah'],
    'shipping' => [
        ['label' => 'Ekspedisi', 'value' => 'JNE Reguler'],
        ['label' => 'Jumlah item', 'value' => $ITEMS.' pcs'],
    ],
    'item_has_fee' => false,
    'totals' => [
        ['label' => 'Subtotal', 'value' => format_price(5250000)],
        ['label' => 'Ongkir', 'value' => format_price(60000)],
    ],
    'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price(5310000)],
    'left' => null,
    'paid' => false,
    'payment_text' => 'Silakan transfer, lalu kirim bukti via WhatsApp dengan menyebut nomor invoice #ND20261106-DE30962A.',
    'qris' => null,
    'bank' => $bank,
    'footer' => 'Terima kasih telah menitip di NITIPDIEND',
], ['items' => $items]);

$html = view('invoices.pdf', ['doc' => $doc])->render();

// Juga tulis HTML-nya supaya bisa dianalisis layout-nya tanpa merender PDF
// (mis. dengan tools/measure-footer.mjs untuk mencari penyebab page 2).
$htmlPath = storage_path('app/invoice-qa/heavy.html');
file_put_contents($htmlPath, $html);
echo 'html     : '.$htmlPath.PHP_EOL;

$result = app(InvoicePdfRenderer::class)->render($html);

$path = storage_path('app/invoice-qa/heavy.pdf');
file_put_contents($path, $result['pdf']);

echo 'renderer : '.$result['renderer'].PHP_EOL;
echo 'items    : '.$ITEMS.PHP_EOL;
echo 'bytes    : '.strlen($result['pdf']).PHP_EOL;
echo 'saved    : '.$path.PHP_EOL;
