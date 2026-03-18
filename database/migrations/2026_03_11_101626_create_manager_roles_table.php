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
            $table->bigIncrements('id');
            $table->integer('manager_id')->index('manager_roles_manager_id_foreign');
            $table->string('permission_key');
            $table->timestamps();
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
