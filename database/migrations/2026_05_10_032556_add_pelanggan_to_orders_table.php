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
    Schema::table('orders', function (Blueprint $table) {
        $table->string('nama_pelanggan')->after('barcode_data');
        $table->string('no_hp')->after('nama_pelanggan');
        $table->string('alamat')->nullable()->after('no_hp');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
