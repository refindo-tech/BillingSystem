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
            // $table->foreignId('server_id')->after('id')->nullable()->constrained(Server::class)->onDelete('set null');
            $table->foreignIdFor(Server::class, 'server_id')->after('id')->nullable()->constrained()->onDelete('set null')->comment('ID server yang melakukan pendaftara user.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->dropForeign(['server_id']);
            $table->dropColumn('server_id');
        });
    }
};
