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
    | Payload (EMV) QRIS merchant — hasil decode gambar QRIS yang
    | di-upload admin di Pengaturan -> Pembayaran QRIS.
    |
    | Invoice merender QR bersih dari payload ini (lihat qris_payload()).
    | Kalau QRIS diganti/di-upload ulang, payload ini WAJIB diperbarui
    | (atau isi setting `qris_payload` di database) supaya QR di invoice
    | tetap mengarah ke merchant yang benar.
    */
    'qris_payload' => '00020101021126610014COM.GO-JEK.WWW01189360091438106334420210G8106334420303UMI51440014ID.CO.QRIS.WWW0215ID10243598911980303UMI5204421553033605802ID5917Jastip NITIPDIEND6013JAKARTA BARAT61051174062070703A016304E09D',

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
