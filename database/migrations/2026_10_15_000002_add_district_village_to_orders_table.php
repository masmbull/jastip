<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Simpan kecamatan & kelurahan tujuan pada pesanan. Dipakai form invoice manual
 * saat admin memilih wilayah bertingkat (kota > kecamatan > kelurahan).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('district')->nullable()->after('city');
            $table->string('village')->nullable()->after('district');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['district', 'village']);
        });
    }
};
