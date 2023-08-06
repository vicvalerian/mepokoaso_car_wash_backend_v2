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
        Schema::create('transaksi_pencucians', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('kendaraan_id');
            $table->bigInteger('karyawan_id');
            $table->bigInteger('mobil_pelanggan_id')->nullable();
            $table->string('no_pencucian');
            $table->string('no_polisi');
            $table->string('jenis_kendaraan');
            $table->integer('tarif_kendaraan');
            $table->date('tgl_pencucian');
            $table->time('waktu_pencucian');
            $table->string('status');
            $table->integer('total_pembayaran')->default(0);
            $table->boolean('is_free')->default(false);
            $table->integer('keuntungan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pencucians');
    }
};
