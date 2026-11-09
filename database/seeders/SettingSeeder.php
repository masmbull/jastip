<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Branding
            ['key' => 'brand_name', 'value' => 'NITIP DI END', 'type' => 'string', 'group' => 'branding', 'is_public' => true],
            ['key' => 'brand_tagline', 'value' => 'EH, NITIP DONG!', 'type' => 'string', 'group' => 'branding', 'is_public' => true],
            ['key' => 'brand_owner', 'value' => 'Sample Owner', 'type' => 'string', 'group' => 'branding', 'is_public' => true],
            ['key' => 'brand_logo', 'value' => null, 'type' => 'string', 'group' => 'branding', 'is_public' => true],
            ['key' => 'brand_favicon', 'value' => null, 'type' => 'string', 'group' => 'branding', 'is_public' => true],

            // Contact
            ['key' => 'whatsapp', 'value' => '00000000000', 'type' => 'string', 'group' => 'contact', 'is_public' => true],
            ['key' => 'instagram', 'value' => 'nitipdiend', 'type' => 'string', 'group' => 'contact', 'is_public' => true],
            ['key' => 'tiktok', 'value' => 'nitipdiend', 'type' => 'string', 'group' => 'contact', 'is_public' => true],
            ['key' => 'email', 'value' => 'owner@example.test', 'type' => 'string', 'group' => 'contact', 'is_public' => true],
            ['key' => 'address', 'value' => 'Alamat Contoh, Kota Contoh, Indonesia', 'type' => 'string', 'group' => 'contact', 'is_public' => true],

            // Colors
            ['key' => 'primary_color', 'value' => '#141414', 'type' => 'string', 'group' => 'design', 'is_public' => true],
            ['key' => 'secondary_color', 'value' => '#64748B', 'type' => 'string', 'group' => 'design', 'is_public' => true],
            ['key' => 'accent_color', 'value' => '#FFC800', 'type' => 'string', 'group' => 'design', 'is_public' => true],

            // Site info
            ['key' => 'footer_text', 'value' => '© 2025 NITIP DI END. Didesain khusus untuk jastip lokal Indonesia.', 'type' => 'string', 'group' => 'site', 'is_public' => true],
            ['key' => 'meta_description', 'value' => 'Jastip lokal pilihan Sample Owner. Titip parfum, tumbler, lifestyle, dan produk pilihan dari Indonesia.', 'type' => 'string', 'group' => 'seo', 'is_public' => true],
            ['key' => 'meta_keywords', 'value' => 'jastip, nitip, jastip lokal, parfum, tumbler, lifestyle, fashion, beauty', 'type' => 'string', 'group' => 'seo', 'is_public' => true],
            ['key' => 'shipping_cost', 'value' => '0', 'type' => 'integer', 'group' => 'business', 'is_public' => true],
            ['key' => 'minimum_order', 'value' => '50000', 'type' => 'integer', 'group' => 'business', 'is_public' => true],
            ['key' => 'setbiaya_fee', 'value' => '0', 'type' => 'integer', 'group' => 'business', 'is_public' => true],

            // QRIS payment
            ['key' => 'qris_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'qris', 'is_public' => true],
            ['key' => 'qris_merchant_name', 'value' => 'NITIP DI END', 'type' => 'string', 'group' => 'qris', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        Setting::firstOrCreate(
            ['key' => 'qris_image'],
            ['value' => '', 'type' => 'string', 'group' => 'qris', 'is_public' => true]
        );
    }
}