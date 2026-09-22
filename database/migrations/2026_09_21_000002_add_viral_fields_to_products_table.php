<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom untuk katalog "Produk Viral":
     * brand, rentang harga pasar (price_max), rating, jumlah terjual,
     * serta flag viral + peringkat viralnya.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('name');
            $table->unsignedBigInteger('price_max')->nullable()->after('price');
            $table->decimal('rating', 2, 1)->nullable()->after('stock');
            $table->unsignedInteger('sold_count')->default(0)->after('rating');
            $table->boolean('is_viral')->default(false)->after('is_featured');
            $table->unsignedInteger('viral_rank')->nullable()->after('is_viral');

            $table->index('is_viral');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_viral']);
            $table->dropColumn(['brand', 'price_max', 'rating', 'sold_count', 'is_viral', 'viral_rank']);
        });
    }
};
