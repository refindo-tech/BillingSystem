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
        Schema::create('tb_kas', function (Blueprint $table) {
            $table->integer('id_kas')->primary();
            $table->integer('id_transaksi')->nullable();
            $table->date('tgl_kas')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->integer('penerimaan')->nullable();
            $table->integer('pengeluaran')->nullable();
            $table->string('jenis_kas', 15)->nullable();
            $table->integer('status')->nullable();
            $table->integer('id_tagihan')->nullable();
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kas');
    }
};
