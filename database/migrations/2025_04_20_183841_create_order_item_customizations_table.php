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
        Schema::create('order_item_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_customization_id')->constrained()->onDelete('cascade');
            $table->string('customization_value')->comment('Nilai Kustomisasi');
            $table->string('price')->nullable()->comment('Harga Kustomisasi');
            $table->string('status')->nullable()->comment('Status Kustomisasi');
            $table->string('custom_note')->nullable()->comment('Catatan Kustomisasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_customizations');
    }
};
