<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom setbiaya_fee (biaya / fee layanan) ke tabel products.
     * Nilai dalam Rupiah, nullable — 0 berarti tidak ada fee.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('setbiaya_fee')->default(0)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('setbiaya_fee');
        });
    }
};
