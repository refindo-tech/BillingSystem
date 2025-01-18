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
        Schema::create('tb_tagihan', function (Blueprint $table) {
            $table->integer('id_tagihan')->primary();
            $table->integer('id_pelanggan');
            $table->string('bulan_tahun', 30);
            $table->integer('jml_bayar');
            $table->integer('terbayar')->nullable();
            $table->date('tgl_bayar')->nullable();
            $table->integer('status_bayar')->nullable();
            $table->string('no_invoice', 100)->nullable();
            $table->integer('blokir_status')->nullable();
            $table->enum('terkirim', ['belum', 'terkirim']);
            $table->dateTime('waktu_bayar')->nullable();
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tagihan');
    }
};
