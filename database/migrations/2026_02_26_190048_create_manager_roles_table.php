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
        Schema::create('manager_roles', function (Blueprint $table) {
            $table->id();
            $table->integer('manager_id');
            $table->string('permission_key');
            $table->timestamps();
            $table->foreign('manager_id')->references('manager_id')->on('manager_accounts')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_roles');
    }
};
