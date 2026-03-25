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
        if (!Schema::hasTable('supervisor_activity_logs')) {
            Schema::create('supervisor_activity_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('supervisor_id');
                $table->integer('manager_id');
                $table->string('action');
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisor_activity_logs');
    }
};
