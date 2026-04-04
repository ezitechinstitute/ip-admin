<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('intern_accounts', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('supervisor_id');
                $table->index('manager_id');
            }
        });
        
        // Ensure column exists even if migration already ran
        if (Schema::hasTable('intern_accounts') && !Schema::hasColumn('intern_accounts', 'manager_id')) {
            Schema::table('intern_accounts', function (Blueprint $table) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('supervisor_id');
                $table->index('manager_id');
            });
        }
    }

    public function down()
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            $table->dropIndex(['manager_id']);
            $table->dropColumn('manager_id');
        });
    }
};