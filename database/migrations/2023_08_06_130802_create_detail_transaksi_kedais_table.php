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
        Schema::create('detail_transaksi_kedais', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->bigInteger('transaksi_kedai_id');
            $table->bigInteger('menu_kedai_id');
            $table->integer('kuantitas');
            $table->integer('sub_total');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_kedais');
    }
};
