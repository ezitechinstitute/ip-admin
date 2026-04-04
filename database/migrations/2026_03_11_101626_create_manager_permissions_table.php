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
        if (!Schema::hasTable('manager_permissions')) {
            Schema::create('manager_permissions', function (Blueprint $table) {
                $table->integer('manager_p_id', true);
                $table->integer('manager_id')->index('manager_id');
                $table->integer('tech_id')->index('tech_id');
                $table->enum('interview_type', ['Remote', 'Onsite']);
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_permissions');
    }
};
