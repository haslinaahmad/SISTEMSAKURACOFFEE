<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // xxxx_xx_xx_create_finances_table.php
public function up(): void
{
    Schema::create('finances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('account_id')->constrained()->onDelete('cascade');
        $table->enum('type', ['income', 'expense']);
        $table->string('category'); // Penjualan, Gaji, Listrik, Bahan Baku
        $table->decimal('amount', 15, 2);
        $table->text('description')->nullable();
        $table->date('transaction_date');
        $table->string('reference_proof')->nullable(); // Upload foto struk/bukti
        $table->foreignId('user_id')->constrained(); // Siapa yang input
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
