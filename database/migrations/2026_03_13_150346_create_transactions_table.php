<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id');
            $table->string('inv_id');
            $table->decimal('amount', 10, 2);
            $table->string('type')->default('payment');
            $table->string('method'); // cash, bank_transfer, credit_card, cheque
            $table->text('notes')->nullable();
            $table->date('payment_date');
            $table->unsignedBigInteger('created_by');
            $table->string('created_by_name');
            $table->string('screenshot')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('invoices')
                  ->onDelete('cascade');

            // Indexes for faster queries
            $table->index(['invoice_id', 'payment_date']);
            $table->index('created_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};