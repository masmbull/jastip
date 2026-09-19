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
        ]);

        User::factory()->create([
            'name' => 'Admin Nabila',
            'email' => 'admin@nitipdiend.com',
            'password' => bcrypt('admin123'),
        ]);
    }
}
