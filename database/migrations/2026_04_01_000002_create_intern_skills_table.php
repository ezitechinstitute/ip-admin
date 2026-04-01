<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('intern_skills')) {
            Schema::create('intern_skills', function (Blueprint $table) {
                $table->id();
                $table->integer('intern_id'); // matches int_id in intern_accounts
                $table->string('skill', 100);
                $table->timestamps();
                
                $table->foreign('intern_id')
                      ->references('int_id')
                      ->on('intern_accounts')
                      ->onDelete('cascade');
                      
                $table->index('intern_id');
                $table->unique(['intern_id', 'skill']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_skills');
    }
};