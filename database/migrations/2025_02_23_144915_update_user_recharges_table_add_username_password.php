<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Server;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->string('pppoe_password', 32)->default('123456')->after('username')->comment('Password untuk akun PPP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->dropColumn('pppoe_password');
        });
    }
};
