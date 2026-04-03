<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // if (Schema::hasTable('manager_accounts')) {
        //     Schema::table('manager_accounts', function (Blueprint $table) {
        //         $table->unsignedBigInteger('assigned_manager')->nullable()->after('manager_id');
        //     });
        // }
    }

    public function down(): void
    {
        Schema::table('manager_accounts', function (Blueprint $table) {
            $table->dropColumn('assigned_manager');
        });
    }
};