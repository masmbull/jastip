<?php

/**
 * Render invoice template variants to standalone HTML for visual QA.
 * No database needed: the doc array is built by hand here.
 *
 * Usage: php tools/render-invoice-preview.php
 * Output: storage/app/invoice-qa/{variant}.html
 * Then:  node tools/screenshot-invoice.mjs
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$outDir = storage_path('app/invoice-qa');
if (! is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

$baseDir = 'file:///' . str_replace('\\', '/', base_path());
$logo = $baseDir . '/storage/app/public/settings/logo.jpg';
$qrisImg = $baseDir . '/storage/app/public/settings/qris.jpg';

$bank = ['lines' => bank_account_lines(), 'accounts' => config('invoice.accounts', []), 'text' => bank_account_text()];

$doc = function (array $o) use ($logo, $qrisImg, $bank) {
    return array_merge([
        'brand' => 'NITIPDIEND',
        'logo' => $logo,
        'owner' => 'Nabila Adriyana',
        'store_wa' => '6282336534191',
        'title' => 'TAGIHAN',
        'number' => 'ND20261106-DE30962A',
        'date' => '06 Nov 2026, 03:03 WIB',
        'customer' => ['name' => 'Nabila Adriyana', 'wa' => '082336534191', 'address' => 'Jakardah'],
        'shipping' => [
            ['label' => 'Ekspedisi', 'value' => '-'],
            ['label' => 'Jumlah item', 'value' => '4 pcs'],
        ],
        'items' => [[
            'name' => 'Hampers SOG Sarong Songket',
            'unit' => 'pcs', 'qty' => 4,
            'price' => format_price(46000),
            'subtotal' => format_price(184000),
        ]],
        'totals' => [
            ['label' => 'Subtotal', 'value' => format_price(184000)],
            ['label' => 'Ongkir', 'value' => format_price(0)],
            ['label' => 'Deposit masuk', 'value' => '-' . format_price(40000), 'green' => true],
        ],
        'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price(184000)],
        'left' => ['label' => 'Sisa Bayar', 'value' => format_price(144000)],
        'paid' => false,
        'payment_text' => 'Silakan transfer, lalu kirim bukti via WhatsApp.',
        'qris' => ['image' => $qrisImg, 'merchant' => 'NITIPDIEND'],
        'bank' => $bank,
        'footer' => 'Terima kasih telah menitip di NITIPDIEND',
    ], $o);
};

$variants = [];

$variants['base'] = $doc([]);

// Case of image 1 (Bu Vonny): single item 35.000, QRIS tersedia.
$variants['vonny'] = $doc([
    'customer' => ['name' => 'Bu Vonny', 'wa' => '082494024883', 'address' => 'Jakarta'],
    'shipping' => [
        ['label' => 'Ekspedisi', 'value' => '-'],
        ['label' => 'Jumlah item', 'value' => '1 pcs'],
    ],
    'items' => [[
        'name' => 'Dettol Plester Antiseptik',
        'unit' => 'pcs', 'qty' => 1,
        'price' => format_price(35000),
        'subtotal' => format_price(35000),
    ]],
    'totals' => [
        ['label' => 'Subtotal', 'value' => format_price(35000)],
        ['label' => 'Ongkir', 'value' => format_price(0)],
    ],
    'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price(35000)],
    'left' => null,
    'qris' => ['image' => $qrisImg, 'merchant' => 'JASTIP NITIPDIEND'],
]);

// Case of image 2 (Bu Hesty): multiple items, total > QRIS limit (500rb),
// down payment diterima 200rb, sisa 320rb, payment = bank only.
$hestyItems = [
    ['name' => 'Hampers SOG - Sarong Songket', 'unit' => 'pcs', 'qty' => 3, 'price' => format_price(103000), 'subtotal' => format_price(309000)],
];
foreach (['Blue Sky / Standar', 'Mauve / Standar', 'Matcha / Standar', 'Navy / Standar', 'Pale Cream / Standar'] as $v) {
    $hestyItems[] = ['name' => 'Hampers SOG Hijab / Royal Garden / ' . $v, 'unit' => 'pcs', 'qty' => 1, 'price' => format_price(65000), 'subtotal' => format_price(65000)];
}
$variants['hesty'] = $doc([
    'title' => 'TAGIHAN',
    'number' => 'ND20261104-2A814FDA',
    'date' => '04 Nov 2026, 16:47 WIB',
    'status' => 'dp',
    'customer' => ['name' => 'Bu Hesty', 'wa' => '085771216563', 'address' => 'Jakarta'],
    'shipping' => [
        ['label' => 'Ekspedisi', 'value' => '-'],
        ['label' => 'Jumlah item', 'value' => '8 pcs'],
    ],
    'items' => $hestyItems,
    'totals' => [
        ['label' => 'Subtotal', 'value' => format_price(520000)],
        ['label' => 'Ongkir', 'value' => format_price(0)],
    ],
    'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price(520000)],
    'left' => ['label' => 'Sisa Bayar', 'value' => format_price(320000)],
    'paid_rows' => [['label' => 'Deposit masuk', 'value' => '-' . format_price(200000), 'green' => true]],
    'qris' => null,
    'bank' => $bank,
]);

// Long names + jastip fee column + shipping + deposit + long customer/holder.
$longBank = $bank;
$longBank['accounts'] = [
    ['bank' => 'BCA', 'number' => '2700398369', 'name' => 'Nabila Adriyana Putri Maharani Dewanti'],
    ['bank' => 'SeaBank', 'number' => '901499244727', 'name' => 'Nabila Adriyana Putri Maharani Dewanti'],
    ['bank' => 'BluBCA', 'number' => '001081568345', 'name' => 'Nabila Adriyana Putri Maharani Dewanti'],
];
$longBank['lines'] = array_map(fn ($a) => $a['bank'] . ' ' . $a['number'] . ' a/n ' . $a['name'], $longBank['accounts']);
$variants['long'] = $doc([
    'customer' => [
        'name' => 'Kartika Sari Dewi Anggraini Putri Maharani',
        'wa' => '628123456789012',
        'address' => 'Jl. Merdeka Raya No. 88 RT 04/RW 07, Kelurahan Sukamaju, Kecamatan Coblong, Bandung, Jawa Barat 40132',
    ],
    'shipping' => [
        ['label' => 'Ekspedisi', 'value' => 'JNE YES (Express)'],
        ['label' => 'Jumlah item', 'value' => '5 pcs'],
    ],
    'items' => [
        ['name' => 'Uniqlo Heattech Extra Warm Thermal Long Sleeve Original Jepang', 'unit' => 'pcs', 'qty' => 2, 'price' => format_price(289000), 'fee' => format_price(15000), 'subtotal' => format_price(578000)],
        ['name' => 'Gentle Monster Her 01 Sunglasses Black', 'unit' => 'pcs', 'qty' => 1, 'price' => format_price(3200000), 'fee' => format_price(50000), 'subtotal' => format_price(3200000)],
        ['name' => 'Snack Korea Import Samyang Buldak Original Pack Hot Chicken Flavor Level 2', 'unit' => 'pack', 'qty' => 2, 'price' => format_price(45000), 'fee' => format_price(3000), 'subtotal' => format_price(90000)],
    ],
    'item_has_fee' => true,
    'totals' => [
        ['label' => 'Subtotal', 'value' => format_price(3868000)],
        ['label' => 'Ongkir', 'value' => format_price(35000)],
        ['label' => 'Total Fee Jastip', 'value' => format_price(68000)],
        ['label' => 'Deposit masuk', 'value' => '-' . format_price(500000), 'green' => true],
    ],
    'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price(3971000)],
    'left' => ['label' => 'Sisa Bayar', 'value' => format_price(3471000)],
    'qris' => null, // over QRIS limit -> bank only
    'bank' => $longBank,
]);

// Compact: 14 items triggers compact mode.
$compactItems = [];
for ($i = 1; $i <= 14; $i++) {
    $compactItems[] = [
        'name' => 'Produk Jastip Jepang Baris Ke-' . $i . ' (Sample)',
        'unit' => 'pcs', 'qty' => 1,
        'price' => format_price(50000 * $i),
        'subtotal' => format_price(50000 * $i),
    ];
}
$variants['compact'] = $doc([
    'items' => $compactItems,
    'shipping' => [
        ['label' => 'Ekspedisi', 'value' => 'JNE Reguler'],
        ['label' => 'Jumlah item', 'value' => '14 pcs'],
    ],
    'totals' => [
        ['label' => 'Subtotal', 'value' => format_price(5250000)],
        ['label' => 'Ongkir', 'value' => format_price(60000)],
    ],
    'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price(5310000)],
    'left' => null,
    'qris' => null,
]);

// QRIS only (no bank lines).
$variants['solo-qris'] = $doc(['bank' => ['lines' => [], 'accounts' => [], 'text' => '']]);

// Dual mode with a very long account holder name (clipping stress test).
$dualLongBank = $bank;
$dualLongBank['accounts'] = [
    ['bank' => 'BCA', 'number' => '2700398369', 'name' => 'Nabila Adriyana Putri Maharani Dewanti'],
    ['bank' => 'SeaBank', 'number' => '901499244727', 'name' => 'Nabila Adriyana Putri Maharani Dewanti'],
    ['bank' => 'BluBCA', 'number' => '001081568345', 'name' => 'Nabila Adriyana Putri Maharani Dewanti'],
];
$variants['long-holder'] = $doc(['bank' => $dualLongBank]);

foreach ($variants as $name => $d) {
    $html = view('invoices.pdf', ['doc' => $d])->render();
    file_put_contents($outDir . DIRECTORY_SEPARATOR . $name . '.html', $html);
    echo "rendered {$name}.html\n";
}
