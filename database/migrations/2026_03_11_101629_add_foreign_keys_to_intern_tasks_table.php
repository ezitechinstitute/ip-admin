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
        Schema::table('intern_tasks', function (Blueprint $table) {

            // Add foreign keys cleanly (no custom names)
            $table->foreign('eti_id')
                ->references('eti_id')
                ->on('intern_accounts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // $table->foreign('assigned_by')
            //     ->references('manager_id')
            //     ->on('manager_accounts')
            //     ->cascadeOnUpdate()
            //     ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_tasks', function (Blueprint $table) {
            $table->dropForeign(['eti_id']);
            $table->dropForeign(['assigned_by']);
        });
    }
};
