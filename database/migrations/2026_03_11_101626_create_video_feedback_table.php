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
        Schema::create('video_feedback', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('eti_id');
            $table->string('name');
            $table->string('email');
            $table->string('tech');
            $table->text('videoUrl');
            $table->string('status')->nullable()->default('Pending');
            $table->dateTime('createdAt')->useCurrent();
            $table->dateTime('updatedAt')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_feedback');
    }
};
