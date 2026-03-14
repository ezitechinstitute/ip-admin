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
        Schema::table('invoices', function (Blueprint $table) {
            // Add technology column if not exists
            if (!Schema::hasColumn('invoices', 'technology')) {
                $table->string('technology')->nullable()->after('invoice_type');
            }
            
            // Add approval_status column if not exists
            if (!Schema::hasColumn('invoices', 'approval_status')) {
                $table->string('approval_status')->default('approved')->after('status');
            }
            
            // Add intern_id column if not exists
            if (!Schema::hasColumn('invoices', 'intern_id')) {
                $table->string('intern_id')->nullable()->after('intern_email');
            }
            
            // Add next_due_date column if not exists
            if (!Schema::hasColumn('invoices', 'next_due_date')) {
                $table->date('next_due_date')->nullable()->after('due_date');
            }
            
            // Add notes column if not exists
            if (!Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable()->after('next_due_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'technology',
                'approval_status',
                'intern_id',
                'next_due_date',
                'notes'
            ]);
        });
    }
};