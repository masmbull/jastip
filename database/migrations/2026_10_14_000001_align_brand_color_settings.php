<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD_TO_NEW = [
        'primary_color' => ['#0891B2', '#141414'],
        'secondary_color' => ['#155E75', '#64748B'],
        'accent_color' => ['#22D3EE', '#FFC800'],
    ];

    /**
     * Warna ini tadinya cuma disimpan, tidak dipakai view mana pun. Sekarang
     * jadi sumber variabel CSS, jadi nilai bawaan theme lama harus diganti.
     * Nilai yang sudah diganti admin dilewatkan.
     */
    public function up(): void
    {
        foreach (self::OLD_TO_NEW as $key => [$old, $new]) {
            DB::table('settings')->where('key', $key)->where('value', $old)->update(['value' => $new]);
        }
    }

    public function down(): void
    {
        foreach (self::OLD_TO_NEW as $key => [$old, $new]) {
            DB::table('settings')->where('key', $key)->where('value', $new)->update(['value' => $old]);
        }
    }
};
