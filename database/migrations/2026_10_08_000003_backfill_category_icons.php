<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill icon kategori yang kosong / masih berupa nama heroicon lama
 * (perfume, tumbler, ...) menjadi emoji sesuai label UI "Ikon Emoji".
 * Seeder pakai firstOrCreate sehingga baris existing tidak ikut ter-update;
 * migration ini yang mengisi data lama. Nilai emoji yang sudah diisi admin
 * (karakter non-ASCII) dibiarkan.
 */
return new class extends Migration
{
    private array $emoji = [
        'parfum' => '🧴',
        'tumbler' => '🥤',
        'fashion' => '👕',
        'beauty' => '✨',
        'lifestyle' => '🏠',
        'makanan' => '🍰',
        'accessories' => '⌚',
    ];

    public function up(): void
    {
        foreach ($this->emoji as $slug => $icon) {
            DB::table('categories')
                ->where('slug', $slug)
                ->where(function ($q) {
                    $q->whereNull('icon')
                        ->orWhere('icon', '')
                        ->orWhereRaw("icon NOT GLOB '*[^ -~]*'"); // tanpa karakter emoji/unicode
                })
                ->update(['icon' => $icon]);
        }
    }

    public function down(): void
    {
        // no-op: data icon tidak dikembalikan
    }
};