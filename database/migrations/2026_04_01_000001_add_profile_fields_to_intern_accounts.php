<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            // Add profile fields after existing columns
            if (!Schema::hasColumn('intern_accounts', 'city')) {
                $table->string('city', 100)->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('intern_accounts', 'university')) {
                $table->string('university', 255)->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('intern_accounts', 'bio')) {
                $table->text('bio')->nullable()->after('university');
            }
            
            if (!Schema::hasColumn('intern_accounts', 'image')) {
                $table->string('image', 255)->nullable()->after('bio');
            }
            
            if (!Schema::hasColumn('intern_accounts', 'github')) {
                $table->string('github', 255)->nullable()->after('image');
            }
            
            if (!Schema::hasColumn('intern_accounts', 'linkedin')) {
                $table->string('linkedin', 255)->nullable()->after('github');
            }
            
            if (!Schema::hasColumn('intern_accounts', 'portfolio_url')) {
                $table->string('portfolio_url', 255)->nullable()->after('linkedin');
            }
            
            // Add timestamps if they don't exist
            if (!Schema::hasColumn('intern_accounts', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('intern_accounts', function (Blueprint $table) {
            $columns = ['city', 'university', 'bio', 'image', 'github', 'linkedin', 'portfolio_url'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('intern_accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};