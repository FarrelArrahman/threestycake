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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_stock_id']);
            $table->dropColumn('product_stock_id');
            $table->foreignId('product_id')->constrained()->onDelete('cascade')->after('order_id')->comment('ID Produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->foreignId('product_stock_id')->constrained()->onDelete('cascade')->after('order_id')->comment('ID Stok Produk');
        });
    }
};
