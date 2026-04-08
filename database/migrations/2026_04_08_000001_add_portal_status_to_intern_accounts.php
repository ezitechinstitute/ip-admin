<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            // Add portal_status column if it doesn't exist
            if (!Schema::hasColumn('intern_accounts', 'portal_status')) {
                $table->enum('portal_status', ['pending_activation', 'active', 'frozen'])
                    ->default('pending_activation')
                    ->after('int_status')
                    ->index();
            }
        });

        // Set all existing accounts to 'active' (they were already using system)
        DB::table('intern_accounts')
            ->whereNull('portal_status')
            ->update(['portal_status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('intern_accounts', 'portal_status')) {
                $table->dropColumn('portal_status');
            }
        });
    }
};
