<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('intern_evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('intern_evaluations', 'task_completion')) {
                $table->decimal('task_completion', 5, 2)->default(0)->after('communication');
            }
        });
    }

    public function down()
    {
        Schema::table('intern_evaluations', function (Blueprint $table) {
            $table->dropColumn('task_completion');
        });
    }
};
