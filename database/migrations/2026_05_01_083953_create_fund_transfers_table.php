<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundTransfersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fund_transfers')) {
            Schema::create('fund_transfers', function (Blueprint $table) {
                $table->id();
                $table->string('transfer_id')->unique();
                $table->unsignedBigInteger('from_bank_account_id');
                $table->unsignedBigInteger('to_bank_account_id');
                $table->decimal('amount', 15, 2);
                $table->date('transfer_date');
                $table->text('note')->nullable();
                $table->string('document_path')->nullable();
                $table->string('status')->default('completed');
                $table->string('created_by')->nullable();
                $table->string('created_by_role')->nullable();
                $table->timestamps();

                $table->foreign('from_bank_account_id')
                      ->references('id')->on('bank_accounts')
                      ->onDelete('cascade');
                $table->foreign('to_bank_account_id')
                      ->references('id')->on('bank_accounts')
                      ->onDelete('cascade');

                $table->index('transfer_date');
                $table->index('status');
                $table->index('transfer_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fund_transfers');
    }
}