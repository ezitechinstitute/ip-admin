<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('bank_accounts')) {
            Schema::create('bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_name');
                $table->string('account_number')->nullable();
                $table->string('account_type')->default('bank');
                $table->string('account_sub_type')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0.00);
                $table->decimal('current_balance', 15, 2)->default(0.00);
                $table->text('note')->nullable();
                $table->string('added_by')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index('account_type');
                $table->index('is_active');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
}