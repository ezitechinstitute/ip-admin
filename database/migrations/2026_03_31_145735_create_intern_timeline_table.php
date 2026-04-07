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
        if (!Schema::hasTable('intern_timeline')) {
            Schema::create('intern_timeline', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('intern_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->date('event_date');
                $table->string('type'); // start, project, task, milestone, certificate, end
                $table->string('color')->default('primary'); // primary, success, warning, danger, info
                $table->boolean('completed')->default(false);
                $table->string('link')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable(); // project_id or task_id
                $table->string('reference_type')->nullable(); // project, task
                $table->timestamps();

                // Indexes for performance
                $table->index('intern_id');
                $table->index('event_date');
                $table->index('type');
                $table->index('completed');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_timeline');
    }
};