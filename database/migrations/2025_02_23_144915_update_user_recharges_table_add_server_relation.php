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
        // Schema::table('user_recharges', function (Blueprint $table) {
        //     $table->foreignIdFor(Server::class)->constrained();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('user_recharges', function (Blueprint $table) {
        //     $table->dropForeign(['server_id']);
        //     $table->dropColumn('server_id');
        // });
    }
};
