<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SettingSeeder::class,
            ServiceSeeder::class,
        ]);

        // Data contoh (produk & order dummy) HANYA untuk lokal/testing, supaya
        // katalog produksi bersih dan diisi sendiri oleh admin. Test butuh
        // produk untuk crawl/meta, jadi seedernya tetap dipakai di testing.
        if (app()->environment('local', 'testing')) {
            $this->call([
                ProductSeeder::class,
                ViralProductSeeder::class,
                OrderSeeder::class,
            ]);
        }

        // Password di-hash dengan Hash::make() sebelum disimpan
        User::updateOrCreate(
            ['email' => 'admin@nitipdiend.com'],
            [
                'name' => 'Admin Nabila',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'budi@nitipdiend.com'],
            [
                'name' => 'Budi Santoso',
                'username' => 'budi',
                'password' => Hash::make('customer123'),
                'email_verified_at' => now(),
                'role' => 'customer',
            ]
        );
    }
}
