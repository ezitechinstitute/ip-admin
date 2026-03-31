<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('screenshot')->nullable()->change();
            });
        }
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('screenshot')->nullable(false)->change();
        });
    }
};