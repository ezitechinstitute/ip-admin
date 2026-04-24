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
        Schema::table('intern_table', function (Blueprint $table) {
            // ✅ Make interview_date column nullable
            $table->string('interview_date')->nullable()->change();
            
            // ✅ Make interview_time column nullable
            $table->string('interview_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_table', function (Blueprint $table) {
            // ⚠️ Rollback: Make columns NOT NULL again
            $table->string('interview_date')->nullable(false)->change();
            $table->string('interview_time')->nullable(false)->change();
        });
    }
};