<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Status ketersediaan produk: apakah barang Pre-Order (PO) dan estimasi kapan
 * siap. Dipakai form produk (admin) dan ditampilkan ke pembeli di halaman
 * produk. `ready_date` opsional, diisi mis. tanggal estimasi barang siap.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_po')->default(false)->after('is_viral');
            $table->date('ready_date')->nullable()->after('is_po');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_po', 'ready_date']);
        });
    }
};