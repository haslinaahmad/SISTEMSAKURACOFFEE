<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // xxxx_xx_xx_create_debts_table.php
public function up(): void
{
    Schema::create('debts', function (Blueprint $table) {
        $table->id();
        $table->enum('type', ['payable', 'receivable']); // Hutang (Kita hutang), Piutang (Orang hutang ke kita)
        $table->string('party_name'); // Nama Supplier atau Pelanggan
        $table->decimal('amount', 15, 2); // Total hutang
        $table->decimal('remaining_amount', 15, 2); // Sisa
        $table->date('due_date')->nullable();
        $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
        $table->text('description')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
