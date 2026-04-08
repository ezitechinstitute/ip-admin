<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('supervisor_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('eti_id'); // intern id
            $table->unsignedBigInteger('supervisor_id');

            $table->integer('score');
            $table->text('remarks');
            $table->text('improvement_suggestions')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisor_feedback');
    }
};
