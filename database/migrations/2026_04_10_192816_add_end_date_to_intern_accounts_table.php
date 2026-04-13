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
        Schema::table('intern_accounts', function (Blueprint $table) {
            // We use string() because your start_date is also a varchar(255).
            // nullable() ensures it won't break other modules that don't pass an end_date.
            // after() just places it neatly next to start_date in your database.
            $table->string('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};