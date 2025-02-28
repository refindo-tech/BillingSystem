<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            // $table->foreignId('server_id')->after('id')->nullable()->constrained(Server::class)->onDelete('set null');
            // $table->foreignIdFor(Customer::class, 'customer_id')->after('id')->nullable()->constrained()->onDelete('set null')->comment('ID customer yang melakukan transaksi.');
            // $table->string('pppoe_password', 32)->after('username')->nullable()->comment('Password PPPoE untuk mengakses internet.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            // $table->dropForeign(['customer_id']);
            // $table->dropColumn('customer_id');
            // $table->dropColumn('pppoe_password');
        });
    }
};
