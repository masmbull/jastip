<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetImageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set QRIS image
        Setting::set('qris_image', 'settings/qris.jpg', 'string', 'invoice', true);
        
        // Set brand logo
        Setting::set('brand_logo', 'settings/logo.jpg', 'string', 'brand', true);
    }
}
