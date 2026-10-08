<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Unit yang ditahan oleh pesanan belum-lunas (mencegah overselling).
     * Stok tersedia = stock - reserved.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('reserved')->default(0)->after('stock');
        });

        // Menandai stok pesanan sudah dikunci (dikurangi), agar commit/release idempoten.
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('stock_committed')->default(false)->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('reserved');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('stock_committed');
        });
    }
};