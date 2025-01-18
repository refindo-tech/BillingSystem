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
        Schema::create('tbl_odc', function (Blueprint $table) {
            $table->integer('id_odc')->primary();
            $table->string('nama_odc', 255);
            $table->string('perangkat_odc', 50);
            $table->string('port_odc', 30);
            $table->text('location');
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
        Schema::dropIfExists('tbl_odc');
    }
};
