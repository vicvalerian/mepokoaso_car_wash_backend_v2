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
        // transaksi pencucian
        // peminjaman karyawan
        // karyawan

        Schema::table('transaksi_pencucians', function (Blueprint $table) {
            $table->boolean('is_save_mobil_pelanggan')->default(false)->nullable();
            $table->date('paid_at')->nullable();
        });

        Schema::table('peminjaman_karyawans', function (Blueprint $table) {
            $table->string('alasan')->nullable()->change();
        });

        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('no_telp')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
