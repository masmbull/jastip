<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('file_path')->comment('Path to uploaded proof image (jpg, png)');
            $table->string('file_name')->comment('Original filename');
            $table->integer('file_size')->comment('File size in bytes');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->index();
            $table->text('admin_notes')->nullable()->comment('Admin verification notes');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};
