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
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->integer('id', true);
                $table->decimal('amount', 10);
                $table->string('instructor_email');
                $table->string('manager_email');
                $table->decimal('company_amount', 10);
                $table->decimal('instructor_amout', 10);
                $table->decimal('manager_amount', 10);
                $table->decimal('remaining_amount', 10);
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
