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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->comment('Kode Produk');
            $table->string('name')->comment('Nama Produk');
            $table->string('category')->nullable()->comment('Kategori');
            $table->string('stock')->nullable()->comment('Stok');
            $table->string('unit')->nullable()->comment('Satuan');
            // $table->string('image')->nullable()->comment('Gambar');
            // $table->string('status')->nullable()->comment('Status');
            $table->timestamps();
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
