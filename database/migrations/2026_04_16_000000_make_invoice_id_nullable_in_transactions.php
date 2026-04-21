<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Make invoice_id nullable to support non-invoice transactions (like withdrawals)
            $table->integer('invoice_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('invoice_id')->nullable(false)->change();
        });
    }
};
