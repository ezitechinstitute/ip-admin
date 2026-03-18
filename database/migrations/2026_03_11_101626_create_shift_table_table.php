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
        Schema::create('shift_table', function (Blueprint $table) {
            $table->integer('shift_id', true);
            $table->string('eti_id');
            $table->string('intern_email');
            $table->time('start_shift');
            $table->time('end_shift');
            $table->string('onsite_remote');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();

            $table->unique(['eti_id', 'intern_email'], 'eti_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_table');
    }
};
