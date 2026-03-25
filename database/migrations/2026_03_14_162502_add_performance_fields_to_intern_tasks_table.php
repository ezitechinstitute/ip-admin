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
        if (Schema::hasTable('intern_tasks')) {
            Schema::table('intern_tasks', function (Blueprint $table) {
                $table->boolean('penalty_flag')->default(false)->after('task_status');
                $table->integer('code_quality_score')->nullable()->after('penalty_flag');
                $table->text('remarks')->nullable()->after('review');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_tasks', function (Blueprint $table) {
            $table->dropColumn(['penalty_flag', 'code_quality_score', 'remarks']);
        });
    }
};
