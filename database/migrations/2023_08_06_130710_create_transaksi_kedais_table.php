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
        Schema::create('transaksi_kedais', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('karyawan_id');
            $table->string('no_penjualan');
            $table->integer('total_penjualan');
            $table->date('tgl_penjualan');
            $table->time('waktu_penjualan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_kedais');
    }
};
