<?php

return [
    'groups' => [
        'branding' => [
            'label' => 'Branding',
            'settings' => [
                'brand_name' => ['type' => 'text', 'label' => 'Nama Brand'],
                'brand_tagline' => ['type' => 'text', 'label' => 'Tagline'],
                'brand_owner' => ['type' => 'text', 'label' => 'Pemilik Brand'],
                'brand_logo' => ['type' => 'image', 'label' => 'Logo'],
                'brand_favicon' => ['type' => 'image', 'label' => 'Favicon'],
            ],
        ],
        'contact' => [
            'label' => 'Kontak',
            'settings' => [
                'whatsapp' => ['type' => 'text', 'label' => 'Nomor WhatsApp'],
                'instagram' => ['type' => 'text', 'label' => 'Instagram'],
                'tiktok' => ['type' => 'text', 'label' => 'TikTok'],
                'email' => ['type' => 'email', 'label' => 'Email'],
                'address' => ['type' => 'textarea', 'label' => 'Alamat'],
            ],
        ],
        'design' => [
            'label' => 'Desain',
            'settings' => [
                'primary_color' => ['type' => 'color', 'label' => 'Warna Teks Utama'],
                'secondary_color' => ['type' => 'color', 'label' => 'Warna Teks Sekunder'],
                'accent_color' => ['type' => 'color', 'label' => 'Warna Aksen'],
            ],
        ],
        'site' => [
            'label' => 'Informasi Situs',
            'settings' => [
                'footer_text' => ['type' => 'textarea', 'label' => 'Teks Footer'],
            ],
        ],
        'seo' => [
            'label' => 'SEO',
            'settings' => [
                'meta_description' => ['type' => 'textarea', 'label' => 'Meta Description', 'rows' => 3],
                'meta_keywords' => ['type' => 'text', 'label' => 'Meta Keywords'],
            ],
        ],
        'business' => [
            'label' => 'Pengaturan Bisnis',
            'settings' => [
                'shipping_cost' => ['type' => 'number', 'label' => 'Biaya Ongkir Default (Rp)'],
                'minimum_order' => ['type' => 'number', 'label' => 'Minimum Order (Rp)'],
                'setbiaya_fee' => ['type' => 'number', 'label' => 'Set Biaya Fee Global (Rp)'],
            ],
        ],
        'qris' => [
            'label' => 'Pembayaran QRIS',
            'settings' => [
                'qris_enabled' => ['type' => 'boolean', 'label' => 'Aktifkan Pembayaran QRIS'],
                'qris_merchant_name' => ['type' => 'text', 'label' => 'Nama Merchant QRIS'],
                'qris_image' => ['type' => 'image', 'label' => 'Gambar QRIS Statis (PNG/JPG, upload dari e-wallet kamu)'],
                'qris_max_amount' => ['type' => 'number', 'label' => 'Batas Nominal QRIS (Rp)'],
            ],
        ],
        'rekening' => [
            'label' => 'Rekening Pembayaran',
            'settings' => [
                'bank_name' => ['type' => 'text', 'label' => 'Nama Bank (mis. BCA, Mandiri, BRI)'],
                'bank_account_number' => ['type' => 'text', 'label' => 'Nomor Rekening'],
                'bank_account_name' => ['type' => 'text', 'label' => 'Atas Nama'],
            ],
        ],
    ],
];
