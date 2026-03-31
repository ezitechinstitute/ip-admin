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
        if (!Schema::hasTable('curriculum_templates')) {
            Schema::create('curriculum_templates', function (Blueprint $table) {
                $table->id();
                $table->string('technology');
                $table->string('task_title');
                $table->text('task_description');
                $table->integer('task_duration_days')->default(1);
                $table->decimal('task_mark', 8, 2);
                $table->string('milestone_title')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_templates');
    }
};
