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
        Schema::create('tb_profile', function (Blueprint $table) {
            $table->integer('id_profile')->primary();
            $table->string('nama_sekolah', 255);
            $table->text('alamat');
            $table->string('telpon', 20);
            $table->string('website', 100);
            $table->string('kota', 100);
            $table->string('bendahara', 100);
            $table->string('nip', 30);
            $table->string('foto', 255);
            $table->string('ktu', 255);
            $table->string('nip_ktu', 30);
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
        Schema::dropIfExists('tb_profile');
    }
};
