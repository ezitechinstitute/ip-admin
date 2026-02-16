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
       Schema::table('intern_feedback', function (Blueprint $table) {
        $table->enum('status', ['Open', 'Resolved'])->default('Open')->nullable();
        $table->timestamp('resolved_at')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_feedback', function (Blueprint $table) {
        $table->dropColumn(['status', 'resolved_at']);
    });
    }
};
