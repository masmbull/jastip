<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ViralProductSeeder::class,
            SettingSeeder::class,
            OrderSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'admin@nitipdiend.com'],
            [
                'name' => 'Admin Nabila',
                'username' => 'admin',
                'password' => 'admin123',
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'budi@nitipdiend.com'],
            [
                'name' => 'Budi Santoso',
                'password' => 'customer123',
                'email_verified_at' => now(),
                'role' => 'customer',
            ]
        );
    }
}
