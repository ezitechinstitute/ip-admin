<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('invoice_approvals')) {
            Schema::create('invoice_approvals', function (Blueprint $table) {
                $table->id();
                $table->integer('invoice_id');
                $table->string('inv_id');
                $table->unsignedBigInteger('requested_by');
                $table->string('requested_by_name');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->string('approved_by_name')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('remarks')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('invoice_approvals');
    }
};