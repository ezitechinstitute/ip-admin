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
        Schema::create('invoices', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('inv_id');
            $table->text('screenshot');
            $table->string('name');
            $table->string('contact');
            $table->string('intern_email');
            $table->decimal('total_amount', 10)->default(6000);
            $table->decimal('received_amount', 10);
            $table->decimal('remaining_amount', 10)->default(0);
            $table->string('due_date')->nullable();
            $table->string('received_by');
            $table->string('status');
            $table->dateTime('created_at')->useCurrent();
            $table->string('invoice_type')->default('internship');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
