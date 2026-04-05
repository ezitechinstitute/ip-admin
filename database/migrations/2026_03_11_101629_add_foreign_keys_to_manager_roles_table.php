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
        if (Schema::hasTable('manager_roles')) {
            // Drop constraint first if it exists
            Schema::table('manager_roles', function (Blueprint $table) {
                try { $table->dropForeign('manager_roles_manager_id_foreign'); } catch (\Exception $e) {}
            });
            
            // Add constraint fresh
            Schema::table('manager_roles', function (Blueprint $table) {
                $table->foreign(['manager_id'])->references(['manager_id'])->on('manager_accounts')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manager_roles', function (Blueprint $table) {
            $table->dropForeign('manager_roles_manager_id_foreign');
        });
    }
};
