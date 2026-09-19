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
            SettingSeeder::class,
            OrderSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'admin@nitipdiend.com'],
            [
                'name' => 'Admin Nabila',
                'password' => bcrypt('admin123'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
    }
}
