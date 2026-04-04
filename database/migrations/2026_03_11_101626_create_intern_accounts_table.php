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
        if (!Schema::hasTable('intern_accounts')) {
            Schema::create('intern_accounts', function (Blueprint $table) {
                $table->integer('int_id', true);
                $table->string('eti_id')->index('eti_id');
                $table->string('name');
                $table->string('email');
                $table->string('phone');
                $table->text('password');
                $table->string('int_technology');
                $table->string('start_date')->nullable();
                $table->string('int_status')->default('Test');
                $table->string('review')->nullable();
                $table->text('reset_token')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_accounts');
    }
};
