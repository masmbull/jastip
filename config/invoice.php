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
];