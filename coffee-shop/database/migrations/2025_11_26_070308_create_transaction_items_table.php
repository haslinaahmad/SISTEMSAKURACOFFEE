<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // xxxx_xx_xx_create_transaction_items_table.php
public function up(): void
{
    Schema::create('transaction_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_id')->constrained();
        $table->integer('quantity');
        $table->decimal('price', 15, 2); // Harga saat transaksi terjadi
        $table->decimal('subtotal', 15, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
