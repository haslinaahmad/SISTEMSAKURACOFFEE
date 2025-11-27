<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // xxxx_xx_xx_create_debt_payments_table.php
public function up(): void
{
    Schema::create('debt_payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('debt_id')->constrained()->onDelete('cascade');
        $table->foreignId('account_id')->constrained(); // Bayar pakai apa
        $table->decimal('amount', 15, 2);
        $table->date('payment_date');
        $table->string('proof_image')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
    }
};
