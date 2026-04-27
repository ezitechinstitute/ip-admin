<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions')) {
            if (Schema::hasColumn('transactions', 'instructor_email')) {
                DB::statement("ALTER TABLE transactions MODIFY instructor_email VARCHAR(255) NULL");
            }
            if (Schema::hasColumn('transactions', 'manager_email')) {
                DB::statement("ALTER TABLE transactions MODIFY manager_email VARCHAR(255) NULL");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            if (Schema::hasColumn('transactions', 'instructor_email')) {
                DB::statement("ALTER TABLE transactions MODIFY instructor_email VARCHAR(255) NOT NULL");
            }
            if (Schema::hasColumn('transactions', 'manager_email')) {
                DB::statement("ALTER TABLE transactions MODIFY manager_email VARCHAR(255) NOT NULL");
            }
        }
    }
};