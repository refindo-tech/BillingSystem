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
        Schema::create('tbl_keluhan', function (Blueprint $table) {
            $table->increments('id_keluhan');
            $table->integer('id_pelanggan')->unsigned();
            $table->string('judul_keluhan', 50);
            $table->string('nomor_tiket', 255);
            $table->text('isi_keluhan');
            $table->text('gambar');
            $table->text('masalah')->nullable();
            $table->string('no_wa', 15);
            $table->enum('status_keluhan', ['menunggu', 'proses', 'selesai', 'tidak merespon']);
            $table->dateTime('tanggal');
            $table->integer('user_id')->unsigned()->nullable();
            $table->primary('id_keluhan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_keluhan');
    }
};
