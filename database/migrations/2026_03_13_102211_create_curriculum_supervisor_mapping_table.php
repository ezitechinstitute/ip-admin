<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('curriculum_supervisor_mapping')) {
            Schema::create('curriculum_supervisor_mapping', function (Blueprint $table) {
                $table->id('mapping_id');

                $table->unsignedBigInteger('cp_id');
                $table->foreign('cp_id')->references('cp_id')->on('curriculum_projects')->onDelete('cascade');

                // Match manager_accounts.manager_id type exactly
                $table->integer('supervisor_id'); // use integer(), not unsignedBigInteger()
                $table->foreign('supervisor_id')->references('manager_id')->on('manager_accounts');

                $table->dateTime('assigned_date')->useCurrent();
                $table->integer('assigned_by');
                $table->foreign('assigned_by')->references('manager_id')->on('manager_accounts');

                $table->boolean('is_primary')->default(1);
                $table->boolean('status')->default(1);

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_supervisor_mapping');
    }
};