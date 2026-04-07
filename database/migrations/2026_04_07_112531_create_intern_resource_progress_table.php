<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('intern_resource_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('intern_id');
            $table->unsignedBigInteger('resource_id');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent')->default(0);
            $table->timestamps();
            
            // Add indexes for faster queries
            $table->index('intern_id');
            $table->index('resource_id');
            
            // Prevent duplicate records
            $table->unique(['intern_id', 'resource_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('intern_resource_progress');
    }
};