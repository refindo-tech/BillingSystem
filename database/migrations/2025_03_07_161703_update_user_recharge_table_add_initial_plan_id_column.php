<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->unsignedBigInteger('initial_plan_id')->nullable()->after('plan_id')->comment('ID paket awal yang dipilih oleh pelanggan.');
        });

        DB::statement('UPDATE user_recharges SET initial_plan_id = plan_id');
        
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->foreign('initial_plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->dropForeign(['initial_plan_id']);
            $table->dropColumn('initial_plan_id');
        });
    }
};
