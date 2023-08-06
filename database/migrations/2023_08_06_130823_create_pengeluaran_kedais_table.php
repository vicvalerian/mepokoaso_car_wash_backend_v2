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
        Schema::create('pengeluaran_kedais', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('menu_kedai_id')->nullable();
            $table->string('nama_barang');
            $table->date('tgl_pembelian');
            $table->integer('jumlah_barang');
            $table->integer('harga_pembelian');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_kedais');
    }
};
