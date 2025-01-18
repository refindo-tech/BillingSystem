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
        Schema::create('tbl_blokir', function (Blueprint $table) {
            $table->integer('id_blokir')->primary();
            $table->enum('status_blokir', ['aktif', 'tidakaktif'])->notNullable();
            $table->integer('set_waktu')->nullable();
            $table->string('set_waktu2', 30)->nullable();
            $table->text('pesan_blokir')->nullable();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_blokir');
    }
};
