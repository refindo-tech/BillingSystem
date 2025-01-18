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
        Schema::create('tbl_paketmikrotik', function (Blueprint $table) {
            $table->integer('id_paketmikrotik')->primary();
            $table->enum('status', ['ya', 'tidak'])->notNullable();
            $table->enum('ppn', ['aktif', 'tidak'])->nullable();
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
        Schema::dropIfExists('tbl_paketmikrotik');
    }
};
