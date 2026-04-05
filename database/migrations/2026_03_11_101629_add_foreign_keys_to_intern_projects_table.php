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
        Schema::table('intern_projects', function (Blueprint $table) {

            // Drop if exists (safe way)
            // try {
            //     $table->dropForeign('internkey');
            // } catch (\Exception $e) {}

            // try {
            //     $table->dropForeign('supkey');
            // } catch (\Exception $e) {}

            // Add FK again (clean way)
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
    // public function up(): void
    // {
    //     if (Schema::hasTable('intern_projects')) {
    //         // Drop constraints first if they exist
    //         Schema::table('intern_projects', function (Blueprint $table) {
    //             try { $table->dropForeign('internkey'); } catch (\Exception $e) {}
    //             try { $table->dropForeign('supkey'); } catch (\Exception $e) {}
    //         });
            
    //         // Add constraints fresh
    //         Schema::table('intern_projects', function (Blueprint $table) {
    //             $table->foreign(['eti_id'], 'internkey')->references(['eti_id'])->on('intern_accounts')->onUpdate('restrict')->onDelete('restrict');
    //             $table->foreign(['assigned_by'], 'supkey')->references(['manager_id'])->on('manager_accounts')->onUpdate('restrict')->onDelete('restrict');
    //         });
    //     }
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_projects', function (Blueprint $table) {
            $table->dropForeign(['eti_id']);
            $table->dropForeign(['assigned_by']);
        });
        // Schema::table('intern_projects', function (Blueprint $table) {
        //     $table->dropForeign('internkey');
        //     $table->dropForeign('supkey');
        // });
    }
};
