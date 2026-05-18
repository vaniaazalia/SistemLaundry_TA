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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_order')->unique();
            $table->string('barcode_data')->unique();
            $table->unsignedBigInteger('pelanggan_id')->nullable();
            $table->foreignId('layanan_id')->constrained('layanans');
            $table->decimal('berat_kg', 8, 2);
            $table->decimal('total_harga', 10, 2);
            $table->date('tanggal_masuk');
            $table->date('estimasi_selesai');
            $table->date('tanggal_diambil')->nullable();
            $table->enum('status', [
                'Diproses',
                'Dicuci',
                'Disetrika',
                'Selesai',
                'Sudah Diambil'
            ])->default('Diproses');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
