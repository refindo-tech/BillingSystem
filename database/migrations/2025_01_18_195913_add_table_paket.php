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
        Schema::create('tb_paket', function (Blueprint $table) {
            $table->integer('id_paket')->primary();
            $table->string('nama_paket', 255);
            $table->integer('harga');
            $table->decimal('ppn', 10, 2)->nullable();
            $table->string('id_pmikrotik', 20)->nullable();
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
        Schema::dropIfExists('tb_paket');
    }
};
