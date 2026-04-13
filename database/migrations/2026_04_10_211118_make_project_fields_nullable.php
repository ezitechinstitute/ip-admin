<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Execute raw MySQL to change the columns to allow NULL values
        DB::statement('ALTER TABLE intern_projects MODIFY duration VARCHAR(255) NULL');
        DB::statement('ALTER TABLE intern_projects MODIFY days VARCHAR(255) NULL');
        DB::statement('ALTER TABLE intern_projects MODIFY project_marks VARCHAR(255) NULL');
        DB::statement('ALTER TABLE intern_projects MODIFY obt_marks DECIMAL(8,2) NULL DEFAULT 0');
    }

    public function down(): void
    {
        // If you rollback, change them back to strictly NOT NULL
        DB::statement('ALTER TABLE intern_projects MODIFY duration VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE intern_projects MODIFY days VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE intern_projects MODIFY project_marks VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE intern_projects MODIFY obt_marks DECIMAL(8,2) NOT NULL DEFAULT 0');
    }
};