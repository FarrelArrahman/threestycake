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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable()->comment('Nama Perusahaan');
            $table->string('name')->comment('Nama Supplier');
            $table->string('phone_number')->nullable()->comment('Nomor Telepon');
            $table->string('email')->nullable()->comment('Email');
            $table->string('address')->nullable()->comment('Alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
