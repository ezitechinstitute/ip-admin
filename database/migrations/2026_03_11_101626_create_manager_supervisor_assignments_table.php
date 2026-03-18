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
        Schema::create('manager_supervisor_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('manager_id');
            $table->integer('supervisor_id');
            $table->timestamps();

            $table->unique(['manager_id', 'supervisor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_supervisor_assignments');
    }
};
