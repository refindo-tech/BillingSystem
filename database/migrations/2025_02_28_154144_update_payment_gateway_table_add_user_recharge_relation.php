<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UserRecharge;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->foreignIdFor(UserRecharge::class, 'user_recharge_id')->after('id')->nullable()->constrained()->onDelete('set null')->comment('ID user recharge yang melakukan transaksi.');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropForeign(['user_recharge_id']);
            $table->dropColumn('user_recharge_id');
        });
    }
};
