<?php

/**
 * Gambar statis untuk halaman invoice (relatif ke folder public/).
 * Ganti file-nya kapan saja tanpa ubah kode.
 *
 * Ditaruh di public/ (bukan disk "public") supaya tetap tampil di server
 * bawaan `php artisan serve` yang memblokir path /storage/*.
 */
return [
    // Ditampilkan saat pesanan BELUM lunas.
    'rekening_image' => 'img/invoice/rekening.webp',
    // Ditampilkan saat pesanan SUDAH lunas (bukti pembayaran diterima).
    'paid_image' => 'img/invoice/payment-diterima.jpeg',

    /*
    | Render PNG di server (untuk kirim invoice ke WhatsApp sebagai gambar).
    | Butuh Chrome/Edge headless; kalau kosong, dicari otomatis di lokasi umum
    | (lihat App\Services\InvoiceImageRenderer::CANDIDATES).
    */
    'browser_path' => env('INVOICE_BROWSER_PATH'),
    'image_width' => (int) env('INVOICE_IMAGE_WIDTH', 820),
    'image_timeout' => (int) env('INVOICE_IMAGE_TIMEOUT', 45),

    /*
    | Rekening pembayaran (a/n Nabila Adriyana).
    | Ditampilkan di invoice untuk memudahkan transfer.
    */
    'accounts' => [
        [
            'bank' => 'BCA',
            'number' => '2700398369',
            'name' => 'Nabila Adriyana',
        ],
        [
            'bank' => 'SeaBank',
            'number' => '901499244727',
            'name' => 'Nabila Adriyana',
        ],
        [
            'bank' => 'BluBCA',
            'number' => '001081568345',
            'name' => 'Nabila Adriyana',
        ],
    ],
];
