<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intern_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('intern_tasks', 'grade')) {
                // Remove 'after' clause since 'points' doesn't exist
                $table->decimal('grade', 8, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('intern_tasks', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
    }
};