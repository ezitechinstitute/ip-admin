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
        if (!Schema::hasTable('payment_vouchers')) {
            Schema::create('payment_vouchers', function (Blueprint $table) {
                $table->integer('id', true);
                $table->decimal('amount', 10);
                $table->enum('recipient_type', ['Manager', 'Supervisor']);
                $table->string('recipient_id', 50);
                $table->string('recipient_name', 100);
                $table->string('admin_account_no', 20);
                $table->date('date');
                $table->enum('status', ['Pending', 'Paid'])->nullable()->default('Pending');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
