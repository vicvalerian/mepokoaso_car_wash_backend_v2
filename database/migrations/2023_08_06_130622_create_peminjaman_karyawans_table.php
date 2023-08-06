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
        Schema::create('peminjaman_karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('karyawan_id');
            $table->date('tgl_peminjaman');
            $table->integer('nominal');
            $table->string('alasan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_karyawans');
    }
};
