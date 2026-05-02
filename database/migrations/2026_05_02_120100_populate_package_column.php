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
        // Map existing intern_type values to package values
        DB::table('intern_table')->where('package', null)->update([
            'package' => DB::raw("CASE 
                WHEN intern_type = 'Training Internship' THEN 'training'
                WHEN intern_type = 'Project Practice' THEN 'practice'
                WHEN intern_type = 'Industrial Environment' THEN 'industrial'
                ELSE 'training'
            END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert package values to null
        DB::table('intern_table')->update(['package' => null]);
    }
};
