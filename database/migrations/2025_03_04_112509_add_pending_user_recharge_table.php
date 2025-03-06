<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('pending_user_recharges', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_recharge_id')->constrained()->onDelete('cascade');
        $table->foreignId('customer_id')->constrained()->onDelete('cascade');
        $table->foreignId('plan_id')->constrained()->onDelete('cascade');
        $table->foreignId('router_id')->nullable()->constrained()->onDelete('cascade');
        $table->string('server_id')->nullable();
        $table->string('username');
        $table->string('price');
        $table->enum('status', ['waiting', 'confirmed', 'cancelled'])->default('waiting');
        $table->dateTime('scheduled_for'); // When this should be processed
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_user_recharges');
    }
};
