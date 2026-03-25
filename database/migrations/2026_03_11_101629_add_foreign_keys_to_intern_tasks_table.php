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
                $table->foreign(['eti_id'], 'intenrkey')->references(['eti_id'])->on('intern_accounts')->onUpdate('restrict')->onDelete('restrict');
                $table->foreign(['assigned_by'], 'tasksupkey')->references(['manager_id'])->on('manager_accounts')->onUpdate('restrict')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_tasks', function (Blueprint $table) {
            $table->dropForeign('intenrkey');
            $table->dropForeign('tasksupkey');
        });
    }
};
