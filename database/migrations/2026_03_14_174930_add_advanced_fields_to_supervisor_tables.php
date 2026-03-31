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
        if (Schema::hasTable('intern_projects')) {
            Schema::table('intern_projects', function (Blueprint $table) {
                $table->string('tech_stack')->nullable()->after('title');
                $table->string('difficulty_level')->nullable()->after('tech_stack');
                // Adding more statuses to pstatus enum logic (using string since it's mostly string in DB)
            });
        }

        if (Schema::hasTable('intern_accounts')) {
            Schema::table('intern_accounts', function (Blueprint $table) {
                $table->enum('internship_type', ['Remote', 'Onsite', 'Hybrid'])->default('Remote')->after('int_technology');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_projects', function (Blueprint $table) {
            $table->dropColumn(['tech_stack', 'difficulty_level']);
        });

        Schema::table('intern_accounts', function (Blueprint $table) {
            $table->dropColumn('internship_type');
        });
    }
};
