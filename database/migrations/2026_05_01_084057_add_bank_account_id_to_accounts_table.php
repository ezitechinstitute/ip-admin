<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankAccountIdToAccountsTable extends Migration
{
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'bank_account_id')) {
                $table->unsignedBigInteger('bank_account_id')->nullable()->after('id');
                $table->foreign('bank_account_id')
                      ->references('id')->on('bank_accounts')
                      ->onDelete('set null');
                $table->index('bank_account_id');
            }
        });
    }

    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'bank_account_id')) {
                $table->dropForeign(['bank_account_id']);
                $table->dropColumn('bank_account_id');
            }
        });
    }
}