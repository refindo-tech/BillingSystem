<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('tbl_notif', function (Blueprint $table) {
        $table->increments('id_notifikasi');
        $table->enum('status_notifikasi', ['aktif', 'tidakaktif']);
        $table->text('pesan_notifikasi');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('tbl_notif');
}
};
