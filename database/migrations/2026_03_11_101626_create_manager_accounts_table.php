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
        if (!Schema::hasTable('manager_accounts')) {
            Schema::create('manager_accounts', function (Blueprint $table) {
                $table->integer('manager_id', true);
                $table->string('eti_id');
                $table->text('image');
                $table->string('name');
                $table->string('email');
                $table->string('contact');
                $table->string('join_date');
                $table->string('password');
                $table->double('comission')->default(1000);
                $table->string('department');
                $table->boolean('status')->default(true);
                $table->string('loginas')->default('Manager');
                $table->integer('emergency_contact');
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
        Schema::dropIfExists('manager_accounts');
    }
};
