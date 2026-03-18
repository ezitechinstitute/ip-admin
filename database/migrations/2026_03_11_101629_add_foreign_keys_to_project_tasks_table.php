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
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->foreign(['eti_id'], 'intkey')->references(['eti_id'])->on('intern_accounts')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['project_id'], 'projkey')->references(['project_id'])->on('intern_projects')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['assigned_by'], 'superkey')->references(['manager_id'])->on('manager_accounts')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropForeign('intkey');
            $table->dropForeign('projkey');
            $table->dropForeign('superkey');
        });
    }
};
