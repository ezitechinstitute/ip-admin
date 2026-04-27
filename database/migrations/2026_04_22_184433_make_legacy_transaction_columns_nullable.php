<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions')) {
            $columns = ['instructor_email', 'manager_email', 'company_amount', 'instructor_amout', 'manager_amount', 'remaining_amount', 'payment_date'];
            $modifications = [];
            
            if (Schema::hasColumn('transactions', 'instructor_email')) $modifications[] = "MODIFY instructor_email VARCHAR(255) NULL";
            if (Schema::hasColumn('transactions', 'manager_email')) $modifications[] = "MODIFY manager_email VARCHAR(255) NULL";
            if (Schema::hasColumn('transactions', 'company_amount')) $modifications[] = "MODIFY company_amount DECIMAL(15,2) NULL";
            if (Schema::hasColumn('transactions', 'instructor_amout')) $modifications[] = "MODIFY instructor_amout DECIMAL(15,2) NULL";
            if (Schema::hasColumn('transactions', 'manager_amount')) $modifications[] = "MODIFY manager_amount DECIMAL(15,2) NULL";
            if (Schema::hasColumn('transactions', 'remaining_amount')) $modifications[] = "MODIFY remaining_amount DECIMAL(15,2) NULL";
            if (Schema::hasColumn('transactions', 'payment_date')) $modifications[] = "MODIFY payment_date DATETIME NULL";
            
            if (!empty($modifications)) {
                DB::statement("ALTER TABLE transactions " . implode(",\n            ", $modifications));
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            $modifications = [];
            
            if (Schema::hasColumn('transactions', 'instructor_email')) $modifications[] = "MODIFY instructor_email VARCHAR(255) NOT NULL";
            if (Schema::hasColumn('transactions', 'manager_email')) $modifications[] = "MODIFY manager_email VARCHAR(255) NOT NULL";
            if (Schema::hasColumn('transactions', 'company_amount')) $modifications[] = "MODIFY company_amount DECIMAL(15,2) NOT NULL";
            if (Schema::hasColumn('transactions', 'instructor_amout')) $modifications[] = "MODIFY instructor_amout DECIMAL(15,2) NOT NULL";
            if (Schema::hasColumn('transactions', 'manager_amount')) $modifications[] = "MODIFY manager_amount DECIMAL(15,2) NOT NULL";
            if (Schema::hasColumn('transactions', 'remaining_amount')) $modifications[] = "MODIFY remaining_amount DECIMAL(15,2) NOT NULL";
            if (Schema::hasColumn('transactions', 'payment_date')) $modifications[] = "MODIFY payment_date DATETIME NOT NULL";
            
            if (!empty($modifications)) {
                DB::statement("ALTER TABLE transactions " . implode(",\n            ", $modifications));
            }
        }
    }
};
