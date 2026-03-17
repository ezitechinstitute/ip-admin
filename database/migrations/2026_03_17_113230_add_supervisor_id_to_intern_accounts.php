<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('intern_accounts', 'supervisor_id')) {
                $table->unsignedBigInteger('supervisor_id')->nullable()->after('int_status');
                // Add index for performance (no foreign key constraint)
                $table->index('supervisor_id');
            }
        });
    }

    public function down()
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            $table->dropIndex(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }
};