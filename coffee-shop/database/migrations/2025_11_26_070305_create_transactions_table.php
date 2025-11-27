<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // xxxx_xx_xx_create_transactions_table.php
public function up(): void
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_number')->unique();
        $table->foreignId('user_id')->constrained(); // Kasir
        $table->string('customer_name')->nullable()->default('Guest');
        $table->decimal('subtotal', 15, 2);
        $table->decimal('discount', 15, 2)->default(0);
        $table->decimal('tax', 15, 2)->default(0); // PPN 11%
        $table->decimal('total_amount', 15, 2);
        $table->decimal('cash_amount', 15, 2); // Uang yang dibayar
        $table->decimal('change_amount', 15, 2); // Kembalian
        $table->string('payment_method'); // Cash, QRIS, Transfer
        $table->foreignId('account_id')->nullable()->constrained(); // Masuk ke akun mana
        $table->enum('status', ['paid', 'pending', 'cancelled'])->default('paid');
        $table->text('notes')->nullable();
        $table->timestamps();

        $table->index('created_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
