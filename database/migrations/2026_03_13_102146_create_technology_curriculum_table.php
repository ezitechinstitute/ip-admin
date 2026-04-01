<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('technology_curriculum')) {
            Schema::create('technology_curriculum', function (Blueprint $table) {
            $table->engine = 'InnoDB';    
            $table->id('curriculum_id'); // BIGINT UNSIGNED primary key

                // tech_id matches technologies.tech_id exactly
                $table->integer('tech_id');
                $table->foreign('tech_id')
                      ->references('tech_id')
                      ->on('technologies')
                      ->onDelete('cascade');

                $table->string('curriculum_name');
                $table->text('description')->nullable();
                $table->integer('total_projects')->default(0);
                $table->integer('total_duration_weeks')->default(0);
                $table->boolean('status')->default(1);

                // created_by matches manager_accounts.manager_id exactly
                $table->unsignedBigInteger('created_by');
                $table->foreign('created_by')
                      ->references('manager_id')
                      ->on('manager_accounts');

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('technology_curriculum');
    }
};