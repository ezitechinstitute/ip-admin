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
        if (Schema::hasTable('manager_complaints')) {
            Schema::table('manager_complaints', function (Blueprint $table) {
                $table->foreign(['eti_id'], 'manager_complaints_ibfk_1')->references(['int_id'])->on('intern_accounts')->onUpdate('restrict')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manager_complaints', function (Blueprint $table) {
            $table->dropForeign('manager_complaints_ibfk_1');
        });
    }
};
