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
        Schema::table('manager_payout_requests', function (Blueprint $table) {
            $table->foreign(['manager_id'])->references(['manager_id'])->on('manager_accounts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manager_payout_requests', function (Blueprint $table) {
            $table->dropForeign('manager_payout_requests_manager_id_foreign');
        });
    }
};
