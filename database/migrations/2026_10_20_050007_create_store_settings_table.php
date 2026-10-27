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
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name', 100)->default('INDOMARET POINT');
            $table->string('store_code', 50)->default('IDM-4029');
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->string('receipt_header', 255)->default('SELAMAT DATANG DI INDOMARET');
            $table->string('receipt_footer', 255)->default('TERIMA KASIH, SELAMAT BELANJA KEMBALI');
            $table->string('printer_paper_width', 10)->default('58mm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
