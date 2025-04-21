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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2)->comment('Jumlah Pembayaran');
            $table->string('method')->comment('Metode Pembayaran');
            $table->string('proof_image')->nullable()->comment('Bukti Pembayaran');
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending')->comment('Status Pembayaran');
            $table->string('payment_date')->nullable()->comment('Tanggal Pembayaran');
            $table->string('notes')->nullable()->comment('Catatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
