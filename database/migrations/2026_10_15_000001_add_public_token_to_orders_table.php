<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Token publik pendek untuk link invoice PDF yang dibagikan ke WhatsApp.
 * Menggantikan signed URL yang panjang supaya link di chat rapi: /i/{token}.
 * Token acak 16 karakter, jadi tidak bisa ditebak walau tanpa login.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('public_token', 32)->nullable()->unique()->after('order_number');
        });

        // Isi token untuk order yang sudah ada.
        DB::table('orders')->whereNull('public_token')->orderBy('id')
            ->pluck('id')
            ->each(function ($id) {
                DB::table('orders')->where('id', $id)->update(['public_token' => Str::random(16)]);
            });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
