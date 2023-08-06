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
        Schema::create('detail_transaksi_pencucis', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('transaksi_pencucian_id');
            $table->bigInteger('karyawan_id');
            $table->integer('upah_pencuci');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_pencucis');
    }
};
