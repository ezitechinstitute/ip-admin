<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;  // ✅ ADD THIS LINE

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ Update end_date for Ahmed Raza
        DB::table('intern_accounts')
            ->where('email', 'ahmed.raza@example.com')
            ->whereNull('end_date')
            ->update(['end_date' => '2026-05-15']);
        
        // ✅ Update any other interns with NULL end_date (optional)
        DB::table('intern_accounts')
            ->whereNull('end_date')
            ->whereNotNull('start_date')
            ->update([
                'end_date' => DB::raw("DATE_ADD(start_date, INTERVAL 3 MONTH)")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: Set updated end_dates back to NULL
        DB::table('intern_accounts')
            ->where('email', 'ahmed.raza@example.com')
            ->where('end_date', '2026-05-15')
            ->update(['end_date' => null]);
        
        // Rollback for auto-calculated dates
        DB::table('intern_accounts')
            ->where('end_date', '=', DB::raw("DATE_ADD(start_date, INTERVAL 3 MONTH)"))
            ->update(['end_date' => null]);
    }
};