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
        Schema::create('intern_performance', function (Blueprint $table) {
            $table->id();

            // Intern basic info
            $table->unsignedBigInteger('intern_id');
            $table->string('name');
            $table->string('email');
            $table->string('technology')->nullable();

            // Performance metrics
            $table->float('task_completion')->default(0);
            $table->float('deadline')->default(0);
            $table->float('quality')->default(0);
            $table->float('attendance')->default(0);
            $table->float('overall')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_performance');
    }

};
