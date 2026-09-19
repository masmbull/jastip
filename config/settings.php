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
                'primary_color' => ['type' => 'color', 'label' => 'Warna Background'],
                'secondary_color' => ['type' => 'color', 'label' => 'Warna Teks'],
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
                'shipping_cost' => ['type' => 'number', 'label' => 'Biaya Ongkir (Rp)'],
                'minimum_order' => ['type' => 'number', 'label' => 'Minimum Order (Rp)'],
            ],
        ],
    ],
];