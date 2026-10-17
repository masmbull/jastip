<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Deposit per barang. Admin mengisi deposit tiap item; total deposit pesanan
 * dijumlahkan otomatis (tidak dihitung manual lagi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('deposit')->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('deposit');
        });
    }
};