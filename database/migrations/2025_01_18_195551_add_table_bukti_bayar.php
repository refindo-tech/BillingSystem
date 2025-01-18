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
        Schema::create('tbl_buktibayar', function (Blueprint $table) {
            $table->integer('id_buktibayar')->primary();
            $table->integer('id_rekening');
            $table->integer('id_pelanggan');
            $table->integer('id_tagihan');
            $table->text('gambar');
            $table->text('keterangan');
            $table->dateTime('tanggal_terima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_buktibayar');
    }
};

