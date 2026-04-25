<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE transactions
            MODIFY instructor_email VARCHAR(255) NULL,
            MODIFY manager_email VARCHAR(255) NULL,
            MODIFY company_amount DECIMAL(15,2) NULL,
            MODIFY instructor_amout DECIMAL(15,2) NULL,
            MODIFY manager_amount DECIMAL(15,2) NULL,
            MODIFY remaining_amount DECIMAL(15,2) NULL,
            MODIFY payment_date DATETIME NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE transactions
            MODIFY instructor_email VARCHAR(255) NOT NULL,
            MODIFY manager_email VARCHAR(255) NOT NULL,
            MODIFY company_amount DECIMAL(15,2) NOT NULL,
            MODIFY instructor_amout DECIMAL(15,2) NOT NULL,
            MODIFY manager_amount DECIMAL(15,2) NOT NULL,
            MODIFY remaining_amount DECIMAL(15,2) NOT NULL,
            MODIFY payment_date DATETIME NOT NULL
        ");
    }
};
