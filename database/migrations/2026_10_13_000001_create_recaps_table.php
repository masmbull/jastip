<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rekapan manual admin: lembar rekap bebas (mirip spreadsheet/pivot).
 * Kolom & baris disimpan sebagai JSON supaya admin bisa menambah field/baris
 * sesuai kebutuhan tanpa migrasi baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recaps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('columns')->nullable();
            $table->json('rows')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recaps');
    }
};
