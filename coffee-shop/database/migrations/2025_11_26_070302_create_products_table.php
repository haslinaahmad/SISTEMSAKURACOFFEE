<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // xxxx_xx_xx_create_products_table.php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->string('code')->unique(); // Barcode/SKU
        $table->text('description')->nullable();
        $table->decimal('buy_price', 15, 2);
        $table->decimal('sell_price', 15, 2);
        $table->integer('stock')->default(0);
        $table->integer('min_stock_alert')->default(10);
        $table->string('unit')->default('pcs'); // cup, beans (kg), pcs
        $table->string('image')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->softDeletes();
        
        $table->index('name');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
