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
        if (!Schema::hasTable('intern_attendance')) {
            Schema::create('intern_attendance', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('eti_id');
                $table->string('email');
                $table->dateTime('start_shift');
                $table->dateTime('end_shift')->nullable();
                $table->integer('duration')->default(0);
                $table->boolean('status')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_attendance');
    }
};
