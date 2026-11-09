<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SettingSeeder::class,
            ServiceSeeder::class,
        ]);

        // Demo catalog and orders are only for local development and tests.
        if (app()->environment('local', 'testing')) {
            $this->call([
                ProductSeeder::class,
                ViralProductSeeder::class,
                OrderSeeder::class,
            ]);
        }

        if (! app()->environment('local', 'testing')) {
            return;
        }

        $password = app()->environment('testing')
            ? config('seeding.test_bootstrap_password')
            : config('seeding.bootstrap_password');

        if (! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException('Set SEED_BOOTSTRAP_PASSWORD to at least 12 characters before local seeding.');
        }

        User::updateOrCreate(
            ['email' => 'admin@example.test'],
            [
                'name' => 'Sample Admin',
                'username' => 'admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@example.test'],
            [
                'name' => 'Sample Customer One',
                'username' => 'sample-customer',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'role' => 'customer',
            ]
        );
    }
}
