<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert all hashed passwords to plain text default passwords
        DB::table('intern_accounts')->update([
            'password' => DB::raw("CONCAT('password_', int_id)")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This cannot be reversed as original hashed passwords are lost
        // Passwords have been converted to plain text format
    }
};
