<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisor_roles', function (Blueprint $table) {
            $table->id();

            // Supervisor ID (same as manager_id in your system)
            $table->unsignedBigInteger('supervisor_id');

            // Permission key (e.g. view_supervisor_tasks)
            $table->string('permission_key');

            $table->timestamps();

            // Optional foreign key (safe to skip if unsure)
            // $table->foreign('supervisor_id')
            //       ->references('manager_id')
            //       ->on('manager_accounts')
            //       ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisor_roles');
    }
};