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
        Schema::create('tbl_mikrotik', function (Blueprint $table) {
            $table->integer('id_mikrotik')->primary();
            $table->string('ip', 255);
            $table->string('username', 255);
            $table->string('password', 255);
            $table->string('port_mikrotik', 255)->nullable();
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
        Schema::dropIfExists('tbl_mikrotik');
    }
};
