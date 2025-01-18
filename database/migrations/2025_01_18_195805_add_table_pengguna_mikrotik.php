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
        Schema::create('tbl_penggunamikrotik', function (Blueprint $table) {
            $table->integer('id_penggunamikrotik')->primary();
            $table->enum('status', ['ya', 'tidak']);
            $table->enum('addppsecret', ['ya', 'tidak']);
            $table->enum('ippelanggan', ['statik', 'dynamic']);
            $table->enum('mapping', ['aktif', 'tidak'])->nullable();
            $table->enum('ip_pool', ['ya', 'tidak'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_penggunamikrotik');
    }
};
