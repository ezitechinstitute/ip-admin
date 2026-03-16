<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add invoice_id column if it doesn't exist
            if (!Schema::hasColumn('transactions', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->after('id');
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
    }
};