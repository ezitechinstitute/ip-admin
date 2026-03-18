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
        Schema::create('supervisor_permissions', function (Blueprint $table) {
            $table->integer('sup_p_id', true);
            $table->integer('manager_id')->index('skey');
            $table->integer('tech_id')->index('tkey');
            $table->enum('internship_type', ['Remote', 'Onsite']);
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisor_permissions');
    }
};
