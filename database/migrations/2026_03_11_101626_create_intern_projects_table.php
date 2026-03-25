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
        if (!Schema::hasTable('intern_projects')) {
            Schema::create('intern_projects', function (Blueprint $table) {
                $table->integer('project_id', true);
                $table->string('eti_id')->index('internid');
                $table->string('email', 30);
                $table->string('title');
                $table->string('start_date', 250);
                $table->string('end_date', 250);
                $table->integer('duration');
                $table->integer('days');
                $table->string('project_marks', 250);
                $table->double('obt_marks');
                $table->text('description');
                $table->integer('assigned_by')->index('supkey');
                $table->string('pstatus', 10)->default('Ongoing');
                $table->timestamp('createdat')->useCurrent();
                $table->timestamp('updatedat')->useCurrentOnUpdate()->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_projects');
    }
};
