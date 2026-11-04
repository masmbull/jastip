<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jejak kirim invoice lewat gateway WhatsApp (gambar).
 *
 * Disimpan di orders (bukan tabel log terpisah) supaya daftar Invoice bisa
 * menampilkan "sudah dikirim kapan" tanpa query tambahan per baris.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('wa_invoice_sent_at')->nullable()->after('paid_at');
            $table->string('wa_invoice_error', 255)->nullable()->after('wa_invoice_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['wa_invoice_sent_at', 'wa_invoice_error']);
        });
    }
};
