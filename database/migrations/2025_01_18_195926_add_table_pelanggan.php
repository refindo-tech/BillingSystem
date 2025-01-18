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
        Schema::create('tb_pelanggan', function (Blueprint $table) {
            $table->integer('id_pelanggan')->primary();
            $table->string('kode_pelanggan', 30);
            $table->string('nik', 18)->nullable();
            $table->string('nama_pelanggan', 255);
            $table->string('alamat', 255);
            $table->string('no_telp', 20);
            $table->integer('paket');
            $table->string('ip_address', 255)->nullable();
            $table->dateTime('tgl_pemasangan');
            $table->dateTime('jatuh_tempo')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('location', 255)->nullable();
            $table->string('id_perangkat', 11)->nullable();
            $table->integer('odp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pelanggan');
    }
};
